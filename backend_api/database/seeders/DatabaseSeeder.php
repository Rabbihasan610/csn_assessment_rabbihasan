<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $shopApex = Shop::create([
            'name' => 'Apex Electronics',
            'slug' => 'apex-electronics',
            'description' => 'Your premier destination for high-end gadgets and electronics.',
            'status' => 'active',
        ]);

        $shopMetro = Shop::create([
            'name' => 'Metro Fashion',
            'slug' => 'metro-fashion',
            'description' => 'Trendy apparel and contemporary urban fashion.',
            'status' => 'active',
        ]);

        $admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'shop_id' => null,
        ]);

        $owner1 = User::create([
            'name' => 'Shop Owner 1',
            'email' => 'owner1@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop_owner',
            'shop_id' => $shopApex->id,
        ]);

        $owner2 = User::create([
            'name' => 'Shop Owner 2',
            'email' => 'owner2@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop_owner',
            'shop_id' => $shopMetro->id,
        ]);

        $headphones = Product::create([
            'title' => 'Wireless Noise Cancelling Headphones',
            'slug' => Str::slug('Wireless Noise Cancelling Headphones'),
            'description' => 'High quality studio sound with active noise cancellation.',
            'price' => 199.99,
            'thumbnail' => 'https://images.unsplash.com/photo-1505740420928-5e560c06d30e?w=500&q=80',
            'created_by' => $admin->id,
        ]);

        $keyboard = Product::create([
            'title' => 'Mechanical Gaming Keyboard',
            'slug' => Str::slug('Mechanical Gaming Keyboard'),
            'description' => 'RGB backlit mechanical switches with durable aluminum frame.',
            'price' => 129.50,
            'thumbnail' => 'https://images.unsplash.com/photo-1587829741301-dc798b83add3?w=500&q=80',
            'created_by' => $admin->id,
        ]);

        $monitor = Product::create([
            'title' => 'Ultra HD 4K Smart Monitor',
            'slug' => Str::slug('Ultra HD 4K Smart Monitor'),
            'description' => '32-inch 4K IPS display with 144Hz refresh rate.',
            'price' => 399.00,
            'thumbnail' => 'https://images.unsplash.com/photo-1527443224154-c4a3942d3acf?w=500&q=80',
            'created_by' => $owner1->id,
        ]);

        $shirt = Product::create([
            'title' => 'Slim Fit Cotton Casual Shirt',
            'slug' => Str::slug('Slim Fit Cotton Casual Shirt'),
            'description' => 'Breathable 100% pure organic cotton button down shirt.',
            'price' => 45.00,
            'thumbnail' => 'https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=500&q=80',
            'created_by' => $owner2->id,
        ]);

        $boots = Product::create([
            'title' => 'Classic Leather Chelsea Boots',
            'slug' => Str::slug('Classic Leather Chelsea Boots'),
            'description' => 'Handcrafted genuine leather Chelsea boots with rubber sole.',
            'price' => 120.00,
            'thumbnail' => 'https://images.unsplash.com/photo-1638247025967-b4e38f787b76?w=500&q=80',
            'created_by' => $owner2->id,
        ]);

        ShopProduct::create([
            'shop_id' => $shopApex->id,
            'product_id' => $headphones->id,
            'custom_title' => 'Apex Pro Noise Cancelling Headphones',
            'custom_description' => 'Exclusive Apex edition with enhanced bass response.',
            'custom_price' => 189.99,
            'custom_thumbnail' => 'https://images.unsplash.com/photo-1546435770-a3e426bf472b?w=500&q=80',
            'stock' => 25,
            'is_active' => true,
        ]);

        ShopProduct::create([
            'shop_id' => $shopApex->id,
            'product_id' => $keyboard->id,
            'custom_title' => null,
            'custom_description' => null,
            'custom_price' => 119.00,
            'custom_thumbnail' => null,
            'stock' => 15,
            'is_active' => true,
        ]);

        ShopProduct::create([
            'shop_id' => $shopApex->id,
            'product_id' => $monitor->id,
            'custom_title' => 'Apex 32-inch 4K Studio Display',
            'custom_description' => 'Calibrated for photo and video editing professionals.',
            'custom_price' => 389.00,
            'custom_thumbnail' => null,
            'stock' => 8,
            'is_active' => true,
        ]);

        ShopProduct::create([
            'shop_id' => $shopMetro->id,
            'product_id' => $shirt->id,
            'custom_title' => 'Metro Urban Slim Fit Shirt',
            'custom_description' => 'Metro tailored fit for casual evening wear.',
            'custom_price' => 39.99,
            'custom_thumbnail' => null,
            'stock' => 50,
            'is_active' => true,
        ]);

        ShopProduct::create([
            'shop_id' => $shopMetro->id,
            'product_id' => $boots->id,
            'custom_title' => null,
            'custom_description' => null,
            'custom_price' => 110.00,
            'custom_thumbnail' => null,
            'stock' => 20,
            'is_active' => true,
        ]);
    }
}
