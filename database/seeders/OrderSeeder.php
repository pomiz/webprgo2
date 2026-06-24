<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class OrderSeeder extends Seeder
{
    public function run(): void
    {
        Schema::disableForeignKeyConstraints();

        OrderItem::truncate();
        Order::truncate();

        Schema::enableForeignKeyConstraints();

        // Create 30 orders with explicit status distribution
        $statuses = [
            'pending_payment', 'pending_payment', 'pending_payment', 'pending_payment', 'pending_payment',
            'confirmed', 'confirmed', 'confirmed', 'confirmed', 'confirmed',
            'processing', 'processing', 'processing', 'processing', 'processing',
            'shipped', 'shipped', 'shipped', 'shipped', 'shipped',
            'completed', 'completed', 'completed', 'completed', 'completed',
            'cancelled', 'cancelled', 'cancelled', 'cancelled', 'cancelled',
        ];

        foreach ($statuses as $status) {
            $order = Order::factory()->create(['status' => $status]);

            // 2-5 order items per order
            $itemCount = rand(2, 5);
            for ($i = 0; $i < $itemCount; $i++) {
                OrderItem::factory()->create([
                    'order_id' => $order->id,
                ]);
            }

            // Recalculate subtotal from actual items
            $subtotal = $order->items->sum(fn ($item) => $item->price * $item->quantity);
            $order->update([
                'subtotal' => $subtotal,
                'total_price' => $subtotal + $order->shipping_cost,
            ]);
        }

        $this->command?->info('Created 30 dummy orders with ' . OrderItem::count() . ' order items.');
    }
}
