<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CustomerShopIsolationTest extends TestCase
{
    use RefreshDatabase;

    private Shop $apexShop;
    private Shop $metroShop;
    private Product $headphones;
    private Product $shirt;
    private Product $unassignedCamera;

    protected function setUp(): void
    {
        parent::setUp();

        $this->apexShop = Shop::create([
            'name' => 'Apex Electronics',
            'slug' => 'apex-electronics',
            'status' => 'active',
        ]);

        $this->metroShop = Shop::create([
            'name' => 'Metro Fashion',
            'slug' => 'metro-fashion',
            'status' => 'active',
        ]);

        $this->headphones = Product::create([
            'title' => 'Wireless Headphones',
            'slug' => 'wireless-headphones',
            'description' => 'Original description',
            'price' => 199.99,
            'thumbnail' => 'https://example.com/headphones.jpg',
        ]);

        $this->shirt = Product::create([
            'title' => 'Casual Shirt',
            'slug' => 'casual-shirt',
            'description' => 'Cotton shirt',
            'price' => 45.00,
            'thumbnail' => 'https://example.com/shirt.jpg',
        ]);

        $this->unassignedCamera = Product::create([
            'title' => 'Digital SLR Camera',
            'slug' => 'digital-slr-camera',
            'description' => 'Professional 24MP Camera',
            'price' => 899.00,
            'thumbnail' => 'https://example.com/camera.jpg',
        ]);

        ShopProduct::create([
            'shop_id' => $this->apexShop->id,
            'product_id' => $this->headphones->id,
            'custom_title' => 'Apex Over-Ear Pro Headphones',
            'custom_description' => 'Enhanced bass edition for Apex customers.',
            'custom_price' => 179.99,
            'custom_thumbnail' => 'https://example.com/apex-headphones.jpg',
            'stock' => 15,
            'is_active' => true,
        ]);

        ShopProduct::create([
            'shop_id' => $this->metroShop->id,
            'product_id' => $this->shirt->id,
            'custom_title' => 'Metro Casual Linen Shirt',
            'custom_price' => 39.50,
            'stock' => 30,
            'is_active' => true,
        ]);
    }

    public function test_customer_can_list_active_shops(): void
    {
        $response = $this->getJson('/api/public/shops');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(2, 'data');
    }

    public function test_customer_only_sees_products_assigned_to_specific_shop(): void
    {
        $response = $this->getJson('/api/public/shops/apex-electronics/products');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data.products')
            ->assertJsonPath('data.products.0.product_id', $this->headphones->id)
            ->assertJsonPath('data.products.0.title', 'Apex Over-Ear Pro Headphones')
            ->assertJsonPath('data.products.0.price', 179.99)
            ->assertJsonPath('data.products.0.thumbnail', 'https://example.com/apex-headphones.jpg');
    }

    public function test_unassigned_catalog_product_returns_404_for_shop(): void
    {
        $response = $this->getJson('/api/public/shops/apex-electronics/products/'.$this->unassignedCamera->id);

        $response->assertStatus(404)
            ->assertJsonPath('status', 'error')
            ->assertJsonPath('message', 'Product not found in this shop.');
    }

    public function test_other_shop_product_returns_404_when_accessed_via_different_shop(): void
    {
        $response = $this->getJson('/api/public/shops/apex-electronics/products/'.$this->shirt->id);

        $response->assertStatus(404)
            ->assertJsonPath('status', 'error');
    }

    public function test_customer_can_view_single_assigned_product_detail(): void
    {
        $response = $this->getJson('/api/public/shops/apex-electronics/products/'.$this->headphones->id);

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.product_id', $this->headphones->id)
            ->assertJsonPath('data.title', 'Apex Over-Ear Pro Headphones')
            ->assertJsonPath('data.price', 179.99);
    }
}

