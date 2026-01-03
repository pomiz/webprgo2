<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Nonaktifkan foreign key constraints
        Schema::disableForeignKeyConstraints();

        // Truncate tabel untuk reset data
        User::truncate();
        Order::truncate();
        OrderItem::truncate();

        // Aktifkan kembali foreign key constraints
        Schema::enableForeignKeyConstraints();

        // Buat 1 user admin
        User::factory()->create([
            'name' => 'Admin Ipul',
            'username' => 'ipuladmin',
            'email' => 'ipul@tokobaju.com',
            'password' => Hash::make('ipul12345'),
            'role' => 'admin',
        ]);

        // Jalankan seeder produk
        $this->call([
            ProductSeeder::class,
        ]);
    }
}
