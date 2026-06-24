<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    private static array $couriers = ['JNE', 'J&T', 'SiCepat', 'AnterAja', 'Lion Parcel'];

    public function definition(): array
    {
        $subtotal = fake()->numberBetween(50000, 500000);
        $shippingCost = fake()->numberBetween(10000, 100000);
        $user = User::where('role', 'user')->inRandomOrder()->first();

        // Weighted: fewer pending_payment, more confirmed/processing/shipped
        $status = fake()->randomElement([
            'pending_payment', 'pending_payment',
            'confirmed', 'confirmed',
            'processing', 'processing',
            'shipped', 'shipped', 'shipped',
            'completed', 'completed',
            'cancelled',
        ]);

        $courier = null;
        $trackingNumber = null;
        $shippedAt = null;
        $trackingStatus = null;

        if (in_array($status, ['shipped', 'completed'])) {
            $courier = fake()->randomElement(self::$couriers);
            $trackingNumber = strtoupper(substr($courier, 0, 3)) . '-'
                . fake()->numberBetween(10000000, 99999999);
            $shippedAt = fake()->dateTimeBetween('-14 days', '-1 hour');

            if ($status === 'completed') {
                $trackingStatus = Order::TRACKING_DELIVERED;
            } else {
                $trackingStatus = fake()->randomElement([
                    Order::TRACKING_PICKED_UP,
                    Order::TRACKING_IN_TRANSIT,
                    Order::TRACKING_ARRIVED,
                ]);
            }
        }

        return [
            'user_id' => $user?->id ?? 2, // fallback to faruq
            'subtotal' => $subtotal,
            'shipping_cost' => $shippingCost,
            'total_price' => $subtotal + $shippingCost,
            'shipping_address' => fake()->address(),
            'status' => $status,
            'virtual_account' => 'VA' . date('Ymd') . Str::upper(Str::random(8)),
            'courier' => $courier,
            'tracking_number' => $trackingNumber,
            'shipped_at' => $shippedAt,
            'tracking_status' => $trackingStatus,
            'created_at' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
