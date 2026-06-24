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

        // Category-specific Unsplash fashion images
        $images = match ($category) {
            'Kaos' => [
                'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800',
                'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800',
                'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800',
                'https://images.unsplash.com/photo-1503341504253-dff4815485f1?w=800',
                'https://images.unsplash.com/photo-1554568218-0f1715e72254?w=800',
            ],
            'Kemeja' => [
                'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=800',
                'https://images.unsplash.com/photo-1598033129183-c4f50d736b10?w=800',
                'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800',
                'https://images.unsplash.com/photo-1479064555552-3ef4979f8908?w=800',
                'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=800',
            ],
            'Celana' => [
                'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800',
                'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800',
                'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800',
                'https://images.unsplash.com/photo-1605518216938-7c31b7b14ad0?w=800',
                'https://images.unsplash.com/photo-1584370845323-3ee5ce209eb3?w=800',
            ],
            'Jaket' => [
                'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800',
                'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800',
                'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=800',
                'https://images.unsplash.com/photo-1559551409-dadc876a5be2?w=800',
                'https://images.unsplash.com/photo-1608236415050-315b28041300?w=800',
            ],
            'Aksesoris' => [
                'https://images.unsplash.com/photo-1576871337632-b9aef4c17ab9?w=800',
                'https://images.unsplash.com/photo-1521369909029-2afed882baee?w=800',
                'https://images.unsplash.com/photo-1606760227091-3dd870d97f1d?w=800',
                'https://images.unsplash.com/photo-1509942774463-acf339cf87d5?w=800',
                'https://images.unsplash.com/photo-1511556820780-d912e42b4980?w=800',
            ],
        };

        $img = $images[self::$imageCounter % count($images)];

        return [
            'name' => $name,
            'category' => $category,
            'description' => fake()->sentences(2, true),
            'price' => fake()->randomElement([50000, 75000, 85000, 95000, 110000, 125000, 150000, 175000, 200000, 225000, 250000]),
            'stock' => fake()->numberBetween(10, 100),
            'image' => $img,
        ];
    }
}
