<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Order;
use App\Models\Product;
use App\Models\UserAddress;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutService
{
    public function __construct(
        private ShippingService $shippingService
    ) {}

    /**
     * Get cart items for checkout.
     */
    public function getCartItems(int $userId, array $productIds)
    {
        return CartItem::where('user_id', $userId)
            ->whereIn('product_id', $productIds)
            ->with('product')
            ->get();
    }

    /**
     * Validate stock availability for all cart items.
     * Returns null if valid, or error message string if not.
     */
    public function validateStock($cartItems): ?string
    {
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                return 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.';
            }
        }
        return null;
    }

    /**
     * Calculate subtotal from cart items.
     */
    public function calculateSubtotal($cartItems): float
    {
        return $cartItems->sum(fn($item) => $item->product->price * $item->quantity);
    }

    /**
     * Calculate shipping cost from coordinates.
     */
    public function calculateShipping(float $latitude, float $longitude): float
    {
        $result = $this->shippingService->calculateShippingCost($latitude, $longitude);
        return $result['error'] ? 0 : $result['cost'];
    }

    /**
     * Process the order: create order, decrement stock, clear cart, save address.
     * Returns Order on success, or error string on failure.
     */
    public function processOrder(int $userId, $cartItems, array $data): Order|string
    {
        $subtotal = $this->calculateSubtotal($cartItems);
        $baseShipping = $this->calculateShipping((float) $data['latitude'], (float) $data['longitude']);

        // Calculate actual shipping cost based on courier selection
        $courier = $data['courier'] ?? null;
        $courierName = $data['courier_name'] ?? null;
        $courierEstimate = $data['courier_estimate'] ?? null;
        $shippingCost = $baseShipping;

        if ($courier && $baseShipping > 0) {
            $couriers = $this->shippingService->getCourierOptions($baseShipping);
            $selected = collect($couriers)->firstWhere('code', $courier);
            if ($selected) {
                $shippingCost = $selected['cost'];
            }
        }

        $totalPrice = $subtotal + $shippingCost;

        try {
            DB::beginTransaction();

            $order = Order::create([
                'user_id' => $userId,
                'subtotal' => $subtotal,
                'shipping_cost' => $shippingCost,
                'shipping_address' => $data['shipping_address'] ?? null,
                'total_price' => $totalPrice,
                'status' => Order::STATUS_PENDING_PAYMENT,
                'virtual_account' => 'VA' . date('Ymd') . Str::upper(Str::random(8)),
                'courier' => $courierName,
            ]);

            foreach ($cartItems as $item) {
                $order->items()->create([
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => $item->product->price,
                ]);

                $affected = Product::where('id', $item->product_id)
                    ->where('stock', '>=', $item->quantity)
                    ->decrement('stock', $item->quantity);

                if (!$affected) {
                    DB::rollBack();
                    return 'Stok untuk produk ' . $item->product->name . ' tidak mencukupi.';
                }
            }

            // Clear cart
            CartItem::where('user_id', $userId)
                ->whereIn('product_id', $cartItems->pluck('product_id')->toArray())
                ->delete();

            // Save address
            $this->saveAddress($userId, $data);

            DB::commit();
            return $order;
        } catch (\Exception $e) {
            DB::rollBack();
            return 'Terjadi kesalahan saat memproses pesanan.';
        }
    }

    /**
     * Save user address for future use.
     */
    private function saveAddress(int $userId, array $data): void
    {
        UserAddress::where('user_id', $userId)->update(['is_default' => false]);
        UserAddress::updateOrCreate(
            [
                'user_id' => $userId,
                'province' => $data['province'] ?? null,
                'city' => $data['city'] ?? null,
            ],
            [
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'full_address' => $data['shipping_address'] ?? null,
                'is_default' => true,
            ]
        );
    }
}
