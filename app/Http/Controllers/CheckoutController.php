<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Handle the checkout process, create order, and redirect to invoice.
     */
    public function checkout(Request $request)
    {
        // 1. Validasi & Ambil Data
        $selectedProductIds = $request->input('selected_products');
        if (empty($selectedProductIds)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih untuk checkout.');
        }

        $cart = Session::get('cart', []);
        $totalPrice = 0;
        $itemsToPurchase = [];

        // 2. Kalkulasi Total & Cek Stok
        foreach ($selectedProductIds as $id) {
            if (isset($cart[$id])) {
                $productInDb = Product::find($id);
                // Cek jika stok mencukupi
                if ($productInDb->stock < $cart[$id]['quantity']) {
                    return redirect()->route('cart.index')->with('error', 'Stok untuk produk ' . $cart[$id]['name'] . ' tidak mencukupi.');
                }
                $itemsToPurchase[$id] = $cart[$id];
                $totalPrice += $cart[$id]['price'] * $cart[$id]['quantity'];
            }
        }

        $order = null;

        // 3. Proses ke Database (Gunakan Transaksi)
        try {
            DB::beginTransaction();

            // Buat Order utama
            $order = Order::create([
                'user_id' => Auth::id(),
                'total_price' => $totalPrice,
                'status' => 'paid', // Langsung paid sesuai permintaan
                'virtual_account' => 'VA' . date('Ymd') . Str::upper(Str::random(8)),
            ]);

            // Buat Order Items & Update Stok
            foreach ($itemsToPurchase as $id => $details) {
                $order->items()->create([
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);

                // Kurangi stok produk
                $product = Product::find($id);
                $product->stock -= $details['quantity'];
                $product->save();
            }

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            // Sebaiknya di-log errornya
            return redirect()->route('cart.index')->with('error', 'Terjadi kesalahan saat memproses pesanan. Silakan coba lagi.');
        }

        // 4. Hapus item yang sudah di-checkout dari keranjang session
        $newCart = $cart;
        foreach ($selectedProductIds as $id) {
            unset($newCart[$id]);
        }
        Session::put('cart', $newCart);

        // 5. Redirect ke halaman Invoice
        return redirect()->route('invoice.show', $order);
    }

    /**
     * Display the invoice for a given order.
     */
    public function invoice(Order $order)
    {
        // Pastikan user hanya bisa melihat invoice miliknya sendiri
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('checkout.invoice', compact('order'));
    }
}
