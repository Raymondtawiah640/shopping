<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::firstOrCreate([
            'email' => 'raymondtawiah23@gmail.com',
        ], [
            'name' => 'Kiln enterprise',
            'password' => Hash::make('real'),
        ]);

        // Sample categories for testing
        Category::firstOrCreate([
            'name' => 'Electronics',
        ], [
            'description' => 'Electronic devices and gadgets',
        ]);

        Category::firstOrCreate([
            'name' => 'Clothing',
        ], [
            'description' => 'Clothing and fashion items',
        ]);

        Category::firstOrCreate([
            'name' => 'Books',
        ], [
            'description' => 'Books and literature',
        ]);

        Category::firstOrCreate([
            'name' => 'Home & Garden',
        ], [
            'description' => 'Home improvement and garden supplies',
        ]);

        Category::firstOrCreate([
            'name' => 'Sports & Outdoors',
        ], [
            'description' => 'Sports equipment and outdoor gear',
        ]);

        Category::firstOrCreate([
            'name' => 'Beauty & Personal Care',
        ], [
            'description' => 'Beauty products and personal care items',
        ]);

        Category::firstOrCreate([
            'name' => 'Toys & Games',
        ], [
            'description' => 'Toys, games, and entertainment',
        ]);

        Category::firstOrCreate([
            'name' => 'Automotive',
        ], [
            'description' => 'Car parts and automotive accessories',
        ]);
    }
}
