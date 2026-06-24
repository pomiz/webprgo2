<?php

namespace Database\Factories;

use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\OrderItem>
 */
class OrderItemFactory extends Factory
{
    protected $model = OrderItem::class;

    public function definition(): array
    {
        $product = Product::inRandomOrder()->first();

        return [
            'product_id' => $product?->id ?? 1,
            'quantity' => fake()->numberBetween(1, 5),
            'price' => $product?->price ?? 75000, // snapshot harga saat beli
        ];
    }
}
