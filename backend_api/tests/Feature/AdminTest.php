<?php

namespace Tests\Feature;

use App\Models\Product;
use App\Models\Shop;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AdminTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private string $adminToken;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name' => 'System Admin',
            'email' => 'admin@example.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        $this->adminToken = $this->admin->createToken('admin_token')->plainTextToken;
    }

    public function test_admin_can_manage_shops(): void
    {
        $createResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->postJson('/api/admin/shops', [
                'name' => 'Prime Gadgets',
                'description' => 'Flagship electronics store',
                'status' => 'active',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.name', 'Prime Gadgets')
            ->assertJsonPath('data.slug', 'prime-gadgets');

        $shopId = $createResponse->json('data.id');

        $listResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->getJson('/api/admin/shops');

        $listResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->putJson('/api/admin/shops/'.$shopId, [
                'name' => 'Prime Gadgets Superstore',
                'status' => 'active',
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.name', 'Prime Gadgets Superstore');

        $deleteResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->deleteJson('/api/admin/shops/'.$shopId);

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('shops', ['id' => $shopId]);
    }

    public function test_admin_can_create_shop_owners(): void
    {
        $shop = Shop::create([
            'name' => 'Apex Electronics',
            'slug' => 'apex-electronics',
            'status' => 'active',
        ]);

        $response = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->postJson('/api/admin/shop-owners', [
                'name' => 'Store Manager',
                'email' => 'manager@example.com',
                'password' => 'secret123',
                'shop_id' => $shop->id,
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.email', 'manager@example.com')
            ->assertJsonPath('data.role', 'shop_owner')
            ->assertJsonPath('data.shop_id', $shop->id);
    }

    public function test_admin_can_manage_master_catalog_products(): void
    {
        $createResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->postJson('/api/admin/products', [
                'title' => 'Flagship Wireless Headphones',
                'description' => 'Noise cancellation with 40-hour battery life.',
                'price' => 249.99,
                'thumbnail' => 'https://example.com/headphones.jpg',
            ]);

        $createResponse->assertStatus(201)
            ->assertJsonPath('status', 'success')
            ->assertJsonPath('data.title', 'Flagship Wireless Headphones')
            ->assertJsonPath('data.price', 249.99);

        $productId = $createResponse->json('data.id');

        $listResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->getJson('/api/admin/products');

        $listResponse->assertStatus(200)
            ->assertJsonPath('status', 'success');

        $updateResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->putJson('/api/admin/products/'.$productId, [
                'title' => 'Flagship Wireless Headphones Pro',
                'description' => 'Updated specs with spatial audio.',
                'price' => 279.99,
            ]);

        $updateResponse->assertStatus(200)
            ->assertJsonPath('data.price', 279.99);

        $deleteResponse = $this->withHeader('Authorization', 'Bearer '.$this->adminToken)
            ->deleteJson('/api/admin/products/'.$productId);

        $deleteResponse->assertStatus(200);
        $this->assertDatabaseMissing('products', ['id' => $productId]);
    }

    public function test_non_admin_cannot_access_admin_endpoints(): void
    {
        $shop = Shop::create([
            'name' => 'Demo Shop',
            'slug' => 'demo-shop',
            'status' => 'active',
        ]);

        $owner = User::create([
            'name' => 'Owner',
            'email' => 'owner_test@example.com',
            'password' => Hash::make('password'),
            'role' => 'shop_owner',
            'shop_id' => $shop->id,
        ]);

        $ownerToken = $owner->createToken('owner_token')->plainTextToken;

        $response = $this->withHeader('Authorization', 'Bearer '.$ownerToken)
            ->getJson('/api/admin/shops');

        $response->assertStatus(403);
    }
}

