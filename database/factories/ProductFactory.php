<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    private static int $imageCounter = 0;

    public function definition(): array
    {
        self::$imageCounter++;

        $styles = [
            'Oversize', 'Slim Fit', 'Regular Fit', 'Relaxed', 'Vintage',
            'Minimalis', 'Streetwear', 'Casual', 'Sporty', 'Retro',
        ];

        $categories = [
            'Kaos' => [
                'names' => ['Kaos', 'T-Shirt', 'Atasan'],
                'details' => ['Katun Premium', 'Sablon DTF', 'Lengan Pendek', 'Kerah Bulat', 'Graffiti Print'],
            ],
            'Kemeja' => [
                'names' => ['Kemeja', 'Shirt', 'Atasan Formal'],
                'details' => ['Flanel', 'Denim', 'Lengan Panjang', 'Kerah Klasik', 'Motif Garis'],
            ],
            'Celana' => [
                'names' => ['Celana', 'Jogger', 'Chino', 'Cargo'],
                'details' => ['Bahan Twill', 'Elastis Pinggang', 'Tapered Fit', 'Banyak Kantong', 'Katun Stretch'],
            ],
            'Jaket' => [
                'names' => ['Hoodie', 'Jaket', 'Sweater', 'Bomber'],
                'details' => ['Fleece Tebal', 'Tahan Angin', 'Resleting Full', 'Kantong Depan', 'Water Repellent'],
            ],
            'Aksesoris' => [
                'names' => ['Topi', 'Tas', 'Kaos Kaki', 'Ikat Pinggang'],
                'details' => ['Kanvas', 'Adjustable Strap', 'Unisex', 'Limited Edition', 'Warna Netral'],
            ],
        ];

        $category = fake()->randomElement(array_keys($categories));
        $catData = $categories[$category];
        $style = fake()->randomElement($styles);
        $detail = fake()->randomElement($catData['details']);
        $nameType = fake()->randomElement($catData['names']);

        $name = "{$nameType} {$style} {$detail}";

        return [
            'name' => $name,
            'category' => $category,
            'description' => fake()->sentences(2, true),
            'price' => fake()->randomElement([50000, 75000, 85000, 95000, 110000, 125000, 150000, 175000, 200000, 225000, 250000]),
            'stock' => fake()->numberBetween(10, 100),
            'image' => 'https://picsum.photos/800/1000?random=' . self::$imageCounter,
        ];
    }
}
