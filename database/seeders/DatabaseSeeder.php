<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create specific admin and sub-admin users
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => bcrypt('password'),
            'type' => 0, // Admin
        ]);

        User::factory()->create([
            'name' => 'Sub-admin User',
            'email' => 'subadmin@example.com',
            'password' => bcrypt('password'),
            'type' => 1, // Sub-admin
        ]);

            User::factory(5)->create();
//         Product::factory(5)->create();

        $this->call(ProductSeeder::class);

    }
}
