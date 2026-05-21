<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Product;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ✅ Halaman utama user (list produk + filter kategori + search)
    public function index(Request $request)
    {
        // Ambil semua kategori unik dari produk
        $categories = Product::select('category')->distinct()->pluck('category');

        // Ambil input dari query string
        $category = $request->query('category');
        $search = $request->query('search');

        // Buat query dasar
        $productsQuery = Product::query();

        // Terapkan filter kategori jika ada
        $productsQuery->when($category, function ($query, $category) {
            return $query->where('category', $category);
        });

        // Terapkan filter pencarian jika ada
        $productsQuery->when($search, function ($query, $search) {
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        });

        // Ambil hasil query yang sudah difilter
        $products = $productsQuery->latest()->get();

        // Kirim data ke view
        return view('user.home', compact('products', 'categories', 'category', 'search'));
    }

    // ✅ Detail produk
    public function show($id)
    {
        $product = Product::with(['reviews.user'])->findOrFail($id);
        $previous = Product::where('id', '<', $product->id)->orderBy('id', 'desc')->first();
        $next = Product::where('id', '>', $product->id)->orderBy('id', 'asc')->first();

        $existingReview = null;
        if (auth()->check()) {
            $existingReview = $product->reviews->where('user_id', auth()->id())->first();
        }

        return view('user.detail', compact('product', 'previous', 'next', 'existingReview'));
    }

    // ✅ Tambah ke keranjang (database-backed)
    public function addToCart(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $quantity = max(1, (int) $request->input('quantity', 1));

        $cartItem = CartItem::where('user_id', auth()->id())
            ->where('product_id', $id)
            ->first();

        if ($cartItem) {
            $cartItem->increment('quantity', $quantity);
        } else {
            CartItem::create([
                'user_id' => auth()->id(),
                'product_id' => $id,
                'quantity' => $quantity,
            ]);
        }

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    // ✅ Lihat halaman keranjang
    public function cart()
    {
        $cartItems = CartItem::where('user_id', auth()->id())
            ->with('product')
            ->get();

        return view('user.cart', compact('cartItems'));
    }

    // ✅ Hapus item dari keranjang
    public function removeFromCart($id)
    {
        CartItem::where('user_id', auth()->id())
            ->where('product_id', $id)
            ->delete();

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
