<?php

namespace App\Services\Contracts;

use App\Models\Shop;
use Illuminate\Database\Eloquent\Collection;

interface ShopServiceInterface
{
    public function getAllShops(): Collection;

    public function getActiveShops(): Collection;

    public function findById(int $id): ?Shop;

    public function findBySlug(string $slug): ?Shop;

    public function createShop(array $data): Shop;

    public function updateShop(Shop $shop, array $data): Shop;

    public function deleteShop(Shop $shop): bool;
}

