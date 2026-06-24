<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use Illuminate\Support\Facades\Schema;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // Nonaktifkan foreign key check untuk truncate
        Schema::disableForeignKeyConstraints();
        
        // Hapus data lama biar gak dobel
        Product::truncate();

        // Aktifkan kembali foreign key check
        Schema::enableForeignKeyConstraints();

        // Tambahkan contoh data produk
        $products = [
            [
                'name' => 'Kaos Unisex Oversize',
                'category' => 'Kaos',
                'description' => 'Kaos oversize nyaman untuk kegiatan santai.',
                'price' => 75000,
                'stock' => 50,
                'image' => 'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&auto=format&fit=crop',
            ],
            [
                'name' => 'Hoodie Casual',
                'category' => 'Jaket',
                'description' => 'Hoodie tebal dan nyaman untuk cuaca dingin.',
                'price' => 120000,
                'stock' => 30,
                'image' => 'https://images.unsplash.com/photo-1556157382-97eda2d62296?w=800&auto=format&fit=crop',
            ],
            [
                'name' => 'Celana Jogger',
                'category' => 'Celana',
                'description' => 'Celana jogger cocok untuk aktivitas sehari-hari.',
                'price' => 95000,
                'stock' => 40,
                'image' => 'https://images.unsplash.com/photo-1511707171634-5f897ff02aa9?w=800&auto=format&fit=crop',
            ],
            [
                'name' => 'Kemeja Kotak-Kotak',
                'category' => 'Kemeja',
                'description' => 'Kemeja kasual bergaya unisex untuk tampilan stylish.',
                'price' => 110000,
                'stock' => 25,
                'image' => 'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800&auto=format&fit=crop',
            ],
        ];

        foreach ($products as $product) {
            Product::create($product);
        }

        // Generate 30 dummy products via factory
        Product::factory(30)->create();
    }
}
