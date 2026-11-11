<?php

namespace App\Services;

use App\Models\Shop;
use App\Services\Contracts\ShopServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ShopService implements ShopServiceInterface
{
    public function getAllShops(): Collection
    {
        return Shop::withCount('products', 'users')->latest()->get();
    }

    public function getActiveShops(): Collection
    {
        return Shop::where('status', 'active')->withCount('products')->latest()->get();
    }

    public function findById(int $id): ?Shop
    {
        return Shop::with('users')->find($id);
    }

    public function findBySlug(string $slug): ?Shop
    {
        return Shop::where('slug', $slug)->where('status', 'active')->first();
    }

    public function createShop(array $data): Shop
    {
        return DB::transaction(function () use ($data) {
            $data['slug'] = $this->generateUniqueSlug($data['name']);
            return Shop::create($data);
        });
    }

    public function updateShop(Shop $shop, array $data): Shop
    {
        return DB::transaction(function () use ($shop, $data) {
            if (isset($data['name']) && $data['name'] !== $shop->name) {
                $data['slug'] = $this->generateUniqueSlug($data['name'], $shop->id);
            }

            $shop->update($data);
            return $shop->fresh();
        });
    }

    public function deleteShop(Shop $shop): bool
    {
        return DB::transaction(function () use ($shop) {
            return (bool) $shop->delete();
        });
    }

    private function generateUniqueSlug(string $name, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        $slug = $baseSlug;
        $counter = 1;

        while (Shop::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

