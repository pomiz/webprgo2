<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Models\Order;
use App\Models\UserAddress;
use App\Services\CheckoutService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class CheckoutController extends Controller
{
    public function __construct(
        private CheckoutService $checkoutService
    ) {}

    /**
     * Store selected products in session and redirect to checkout page.
     */
    public function prepare(CheckoutRequest $request)
    {
        Session::put('checkout_products', $request->validated()['selected_products']);
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

        $cartItems = $this->checkoutService->getCartItems(auth()->id(), $selectedProductIds);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        $subtotal = $this->checkoutService->calculateSubtotal($cartItems);
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
            'shipping_address' => 'nullable|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'courier' => 'required|string',
            'courier_name' => 'nullable|string',
            'courier_estimate' => 'nullable|string',
        ]);

        $selectedProductIds = Session::get('checkout_products', []);

        if (empty($selectedProductIds)) {
            return redirect()->route('cart.index')->with('error', 'Tidak ada produk yang dipilih.');
        }

        $cartItems = $this->checkoutService->getCartItems(auth()->id(), $selectedProductIds);

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Keranjang kosong.');
        }

        // Validate stock
        $stockError = $this->checkoutService->validateStock($cartItems);
        if ($stockError) {
            return redirect()->route('cart.index')->with('error', $stockError);
        }

        // Process order
        $result = $this->checkoutService->processOrder(auth()->id(), $cartItems, [
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'shipping_address' => $request->shipping_address,
            'province' => $request->input('province'),
            'city' => $request->input('city'),
            'courier' => $request->courier,
            'courier_name' => $request->courier_name,
            'courier_estimate' => $request->courier_estimate,
        ]);

        if (is_string($result)) {
            return redirect()->route('cart.index')->with('error', $result);
        }

        Session::forget('checkout_products');
        return redirect()->route('invoice.show', $result);
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

    /**
     * Mark the order as paid by the user (simulate payment).
     */
    public function pay(Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        if ($order->status !== Order::STATUS_PENDING_PAYMENT) {
            return redirect()->route('invoice.show', $order)
                ->with('error', 'Pesanan ini sudah dibayar atau dibatalkan.');
        }

        $order->advance(); // pending_payment → confirmed

        return redirect()->route('invoice.show', $order)
            ->with('success', 'Pembayaran berhasil dikonfirmasi! Pesanan Anda akan segera diproses.');
    }
}
