<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ShopOwnerTest extends TestCase
{
    use RefreshDatabase;

    private Shop $shop1;
    private Shop $shop2;
    private User $owner1;
    private User $owner2;
    private string $owner1Token;
    private string $owner2Token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->shop1 = Shop::create([
            'name' => 'Apex Electronics',
            'slug' => 'apex-electronics',
            'status' => 'active',
        ]);

        $this->shop2 = Shop::create([
            'name' => 'Metro Fashion',
            'slug' => 'metro-fashion',
            'status' => 'active',
        ]);

        $this->owner1 = User::create([
            'name' => 'Shop Owner 1',
            'email' => 'owner1@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop_owner',
            'shop_id' => $this->shop1->id,
        ]);

        $this->owner2 = User::create([
            'name' => 'Shop Owner 2',
            'email' => 'owner2@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop_owner',
            'shop_id' => $this->shop2->id,
        ]);

        $this->owner1Token = $this->owner1->createToken('owner1_token')->plainTextToken;
        $this->owner2Token = $this->owner2->createToken('owner2_token')->plainTextToken;
    }

    public function test_owner_can_browse_global_catalog(): void
    {
        Product::create([
            'title' => 'Global Smart Watch',
            'slug' => 'global-smart-watch',
            'price' => 199.99,
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->owner1Token)
            ->getJson('/api/shop-owner/catalog');

        $response->assertStatus(200)
            ->assertJsonPath('status', 'success')
            ->assertJsonCount(1, 'data');
    }

    public function test_owner_can_create_product_into_global_catalog_and_other_owner_sees_it(): void
    {
        $createResponse = $this->withHeader('Authorization', 'Bearer '.$this->owner1Token)
            ->postJson('/api/shop-owner/catalog', [
                'title' => 'Wireless Gaming Mouse',
                'description' => '16000 DPI sensor with RGB',
                'price' => 79.99,
                'thumbnail' => 'https://example.com/mouse.jpg',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Wireless Gaming Mouse');

        $owner2Response = $this->withHeader('Authorization', 'Bearer '.$this->owner2Token)
            ->getJson('/api/shop-owner/catalog');

        $owner2Response->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $titles = collect($owner2Response->json('data'))->pluck('title')->all();
        $this->assertContains('Wireless Gaming Mouse', $titles);
    }

    public function test_owner_can_attach_product_with_custom_overrides(): void
    {
        $product = Product::create([
            'title' => 'Standard Gaming Keyboard',
            'slug' => 'standard-gaming-keyboard',
            'price' => 99.00,
            'thumbnail' => 'https://example.com/kb.jpg',
        ]);

        $attachResponse = $this->withHeader('Authorization', 'Bearer '.$this->owner1Token)
            ->postJson('/api/shop-owner/my-shop/products/attach', [
                'product_id' => $product->id,
                'custom_title' => 'Apex Custom RGB Keyboard',
                'custom_price' => 89.99,
                'custom_thumbnail' => 'https://example.com/custom-kb.jpg',
                'stock' => 30,
            ]);

        $attachResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Apex Custom RGB Keyboard')
            ->assertJsonPath('data.price', 89.99)
            ->assertJsonPath('data.thumbnail', 'https://example.com/custom-kb.jpg')
            ->assertJsonPath('data.stock', 30);

        $listResponse = $this->withHeader('Authorization', 'Bearer '.$this->owner1Token)
            ->getJson('/api/shop-owner/my-shop/products');

        $listResponse->assertStatus(200)
            ->assertJsonCount(1, 'data');
    }

    public function test_owner_can_update_and_detach_shop_product(): void
    {
        $product = Product::create([
            'title' => 'Studio Monitor 27',
            'slug' => 'studio-monitor-27',
            'price' => 299.00,
        ]);

        $shopProduct = ShopProduct::create([
            'shop_id' => $this->shop1->id,
            'product_id' => $product->id,
            'custom_price' => 280.00,
            'stock' => 10,
            'is_active' => true,
        ]);

        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$this->owner1Token)
            ->putJson('/api/shop-owner/my-shop/products/'.$shopProduct->id, [
                'custom_price' => 269.99,
                'stock' => 15,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.price', 269.99)
            ->assertJsonPath('data.stock', 15);

        $deleteResponse = $this->withHeader('Authorization', 'Bearer '.$this->owner1Token)
            ->deleteJson('/api/shop-owner/my-shop/products/'.$shopProduct->id);

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('shop_products', ['id' => $shopProduct->id]);
    }
}

