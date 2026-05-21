<?php

namespace App\Http\Controllers;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Http\Requests\CheckoutRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Handle the checkout process, create order, and redirect to invoice.
     */
    public function checkout(CheckoutRequest $request)
    {
        // 1. Validasi & Ambil Data
        $selectedProductIds = $request->validated()['selected_products'];

        $cartItems = CartItem::where('user_id', auth()->id())
            ->whereIn('product_id', $selectedProductIds)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // 2. Kalkulasi Total & Cek Stok
        $totalPrice = 0;
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.');
            }
            $totalPrice += $item->product->price * $item->quantity;
        }

        $order = null;

        // 3. Proses ke Database (Gunakan Transaksi)
        try {
            DB::beginTransaction();

            // Buat Order utama
            $order = Order::create([
                'user_id' => auth()->id(),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'virtual_account' => 'VA' . date('Ymd') . Str::upper(Str::random(8)),
            ]);

            // Buat Order Items & Update Stok
            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            // Hapus item yang sudah di-checkout dari keranjang
            CartItem::where('user_id', auth()->id())
                ->whereIn('product_id', $selectedProductIds)
                ->delete();

            DB::commit();

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Terjadi kesalahan saat memproses pesanan.');
        }

        // 4. Redirect ke halaman Invoice
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
