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
            ['email' => 'ipul@tokobaju.com'],
            [
                'username' => 'ipul',
                'name' => 'Admin Ipul',
                'password' => 'ipul12345',
                'role' => 'admin',
            ]
        );
        $this->command->info('User admin (ipul@tokobaju.com) ready.');

        // Regular user
        User::firstOrCreate(
            ['email' => 'faruq@tokobaju.com'],
            [
                'username' => 'faruq',
                'name' => 'Faruq',
                'password' => 'faruq1234',
                'role' => 'user',
            ]
        );
        $this->command->info('User (faruq@tokobaju.com) ready.');

        // Seed locations and products
        $this->call([
            LocationSeeder::class,
            ProductSeeder::class,
        ]);
    }
}
