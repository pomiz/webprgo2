<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ✅ Halaman utama user (list produk + filter kategori)
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
        // Ambil produk berdasarkan ID
        $product = Product::findOrFail($id);

        // Ambil produk sebelumnya (ID lebih kecil)
        $previous = Product::where('id', '<', $product->id)->orderBy('id', 'desc')->first();

        // Ambil produk selanjutnya (ID lebih besar)
        $next = Product::where('id', '>', $product->id)->orderBy('id', 'asc')->first();

        // Kirim ke view user.detail
        return view('user.detail', compact('product', 'previous', 'next'));
    }

    // ✅ Tambah ke keranjang (pakai session)
    public function addToCart($id)
    {
        $product = Product::findOrFail($id);

        // Ambil cart dari session, kalau belum ada buat array kosong
        $cart = session()->get('cart', []);

        // Cek apakah produk sudah ada di keranjang
        if (isset($cart[$id])) {
            $cart[$id]['quantity']++;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => 1,
                "price" => $product->price,
                "image" => $product->image,
            ];
        }

        // Simpan kembali ke session
        session()->put('cart', $cart);

        return redirect()->back()->with('success', 'Produk berhasil ditambahkan ke keranjang!');
    }

    // ✅ Lihat halaman keranjang
    public function cart()
    {
        $cart = session()->get('cart', []);
        return view('user.cart', compact('cart'));
    }

    // ✅ Hapus item dari keranjang
    public function removeFromCart($id)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
        }

        return redirect()->back()->with('success', 'Produk dihapus dari keranjang.');
    }
}
