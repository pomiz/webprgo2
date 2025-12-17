<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class UserController extends Controller
{
    // ✅ Halaman utama user (list produk + filter kategori + search)
    public function index(Request $request)
    {
        // Ambil semua kategori unik dari produk
        $categories = Product::select('category')->distinct()->pluck('category');

        // Ambil parameter dari query string
        $category = $request->query('category');
        $search = $request->query('search');

        // Query produk dengan filter kategori dan search
        $products = Product::when($category, function ($query, $category) {
            return $query->where('category', $category);
        })->when($search, function ($query, $search) {
            // Search di multiple fields: name, description, category
            return $query->where(function ($q) use ($search) {
                $q->where('name', 'LIKE', '%' . $search . '%')
                  ->orWhere('description', 'LIKE', '%' . $search . '%')
                  ->orWhere('category', 'LIKE', '%' . $search . '%');
            });
        })->latest()->get();

        // Kirim ke view
        return view('user.home', compact('products', 'categories', 'category', 'search'));
    }

    // ✅ Detail produk
    public function show($id)
    {
        // Ambil produk berdasarkan ID
        $product = Product::findOrFail($id);

        // Kirim ke view user.detail
        return view('user.detail', compact('product'));
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
