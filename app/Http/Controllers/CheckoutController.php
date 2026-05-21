<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserAddress;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Store selected products in session and redirect to checkout page.
     */
    public function prepare(CheckoutRequest $request)
    {
        $selectedProductIds = $request->validated()['selected_products'];
        Session::put('checkout_products', $selectedProductIds);

        return redirect()->route('checkout.index');
    }

    /**
     * Show checkout page with address selection and shipping calculation.
     */
    public function showCheckout()
    {
        $selectedProductIds = Session::get('checkout_products', []);

        if (empty($selectedProductIds)) {
            return redirect()->route('cart.index')->with('error', 'Pilih produk untuk checkout.');
        }

        $cartItems = CartItem::where('user_id', auth()->id())
            ->whereIn('product_id', $selectedProductIds)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subtotal = $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
        $defaultAddress = UserAddress::where('user_id', auth()->id())
            ->where('is_default', true)
            ->first();

        return view('checkout.index', compact('cartItems', 'subtotal', 'defaultAddress'));
    }

    /**
     * Process the checkout with shipping.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $selectedProductIds = Session::get('checkout_products', []);

        if (empty($selectedProductIds)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih.');
        }

        $cartItems = CartItem::where('user_id', auth()->id())
            ->whereIn('product_id', $selectedProductIds)
            ->with('product')
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subtotal = 0;
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return redirect()->route('cart.index')
                    ->with('error', 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.');
            }
            $subtotal += $item->product->price * $item->quantity;
        }

        $shippingCost = (float) $request->shipping_cost;
        $totalPrice = $subtotal + $shippingCost;

        $order = null;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'shipping_address' => $request->shipping_address,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'virtual_account' => 'VA' . date('Ymd') . Str::upper(Str::random(8)),
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $item->product->decrement('stock', $item->quantity);
            }

            // Remove checked-out items from cart
            CartItem::where('user_id', auth()->id())
                ->whereIn('product_id', $selectedProductIds)
                ->delete();

            // Save address for future use
            UserAddress::where('user_id', auth()->id())->update(['is_default' => false]);
            UserAddress::updateOrCreate(
                ['user_id' => auth()->id(), 'is_default' => true],
                [
                    'latitude' => $request->latitude,
                    'longitude' => $request->longitude,
                    'province' => $request->input('province'),
                    'city' => $request->input('city'),
                    'full_address' => $request->shipping_address,
                    'is_default' => true,
                ]
            );

            DB::commit();
            Session::forget('checkout_products');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('cart.index')
                ->with('error', 'Terjadi kesalahan saat memproses pesanan.');
        }

        return redirect()->route('invoice.show', $order);
    }

    /**
     * Display the invoice for a given order.
     */
    public function invoice(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        return view('checkout.invoice', compact('order'));
    }
}
