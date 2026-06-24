<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::firstOrCreate(
            ['email' => 'admin@ruangbaju.com'],
            [
                'username' => 'admin',
                'name' => 'Admin Ruang Baju',
                'password' => 'admin12345',
                'role' => 'admin',
            ]
        );
        $this->command->info('User admin (admin@ruangbaju.com) ready.');

        // Regular user
        User::firstOrCreate(
            ['email' => 'faruq@ruangbaju.com'],
            [
                'username' => 'faruq',
                'name' => 'Faruq',
                'password' => 'faruq1234',
                'role' => 'user',
            ]
        );
        $this->command->info('User (faruq@ruangbaju.com) ready.');

        // Seed locations, products, and orders
        $this->call([
            LocationSeeder::class,
            ProductSeeder::class,
            OrderSeeder::class,
        ]);
    }
}
