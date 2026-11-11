<?php

namespace App\Services\Contracts;

use App\DTOs\ShopProductListingDTO;
use App\Models\Shop;
use App\Models\ShopProduct;
use Illuminate\Database\Eloquent\Collection;

interface ShopProductServiceInterface
{
    public function getShopProducts(int $shopId): Collection;

    public function getPublicShopProducts(Shop $shop): Collection;

    public function getPublicShopProduct(Shop $shop, int $productId): ?ShopProduct;

    public function attachOrUpdateProduct(ShopProductListingDTO $dto): ShopProduct;

    public function updateShopProduct(ShopProduct $shopProduct, array $data): ShopProduct;

    public function detachProduct(ShopProduct $shopProduct): bool;
}

