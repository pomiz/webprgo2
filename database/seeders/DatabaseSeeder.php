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
        User::factory()->create([
            'username' => 'ipul',
            'name' => 'Admin Ipul',
            'email' => 'ipul@tokobaju.com',
            'password' => 'ipul12345',
            'role' => 'admin',
        ]);

        // Regular user
        User::factory()->create([
            'username' => 'faruq',
            'name' => 'Faruq',
            'email' => 'faruq@tokobaju.com',
            'password' => 'faruq1234',
            'role' => 'user',
        ]);

        // Seed locations and products
        $this->call([
            LocationSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
