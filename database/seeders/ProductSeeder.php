<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\User;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::first(); // Ensure there is at least one user in the users table

        for ($i = 1; $i <= 5; $i++) {
            $product = Product::create([
                'name' => "Product $i",
                'status' => rand(0, 1) ? 'active' : 'inactive',
                'code' => $this->generateProductCode(),
                'user_id' => $user->id,
                'description' => "Description for Product $i",
                'price' => rand(10, 100),
            ]);

            $imagePath = 'products/dummy_image.jpg';
            if (!Storage::disk('public')->exists($imagePath)) {
                Storage::disk('public')->put($imagePath, file_get_contents(public_path('dummy_image.jpg')));
            }
            $product->images()->create(['path' => $imagePath]);
        }

    }

    private function generateProductCode()
    {
        return strtoupper(Str::random(10));
    }
}

