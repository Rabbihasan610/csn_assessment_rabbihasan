<?php

namespace App\Services;

use App\DTOs\ShopProductListingDTO;
use App\Models\Shop;
use App\Models\ShopProduct;
use App\Services\Contracts\ShopProductServiceInterface;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class ShopProductService implements ShopProductServiceInterface
{
    public function getShopProducts(int $shopId): Collection
    {
        return ShopProduct::with('product')
            ->where('shop_id', $shopId)
            ->latest()
            ->get();
    }

    public function getPublicShopProducts(Shop $shop): Collection
    {
        return ShopProduct::with('product')
            ->where('shop_id', $shop->id)
            ->where('is_active', true)
            ->latest()
            ->get();
    }

    public function getPublicShopProduct(Shop $shop, int $productId): ?ShopProduct
    {
        return ShopProduct::with('product')
            ->where('shop_id', $shop->id)
            ->where('product_id', $productId)
            ->where('is_active', true)
            ->first();
    }

    public function attachOrUpdateProduct(ShopProductListingDTO $dto): ShopProduct
    {
        return DB::transaction(function () use ($dto) {
            return ShopProduct::updateOrCreate(
                [
                    'shop_id' => $dto->shopId,
                    'product_id' => $dto->productId,
                ],
                [
                    'custom_title' => $dto->customTitle,
                    'custom_description' => $dto->customDescription,
                    'custom_price' => $dto->customPrice,
                    'custom_thumbnail' => $dto->customThumbnail,
                    'stock' => $dto->stock,
                    'is_active' => $dto->isActive,
                ]
            )->load('product');
        });
    }

    public function updateShopProduct(ShopProduct $shopProduct, array $data): ShopProduct
    {
        return DB::transaction(function () use ($shopProduct, $data) {
            $shopProduct->update([
                'custom_title' => array_key_exists('custom_title', $data) ? $data['custom_title'] : $shopProduct->custom_title,
                'custom_description' => array_key_exists('custom_description', $data) ? $data['custom_description'] : $shopProduct->custom_description,
                'custom_price' => array_key_exists('custom_price', $data) ? $data['custom_price'] : $shopProduct->custom_price,
                'custom_thumbnail' => array_key_exists('custom_thumbnail', $data) ? $data['custom_thumbnail'] : $shopProduct->custom_thumbnail,
                'stock' => array_key_exists('stock', $data) ? (int) $data['stock'] : $shopProduct->stock,
                'is_active' => array_key_exists('is_active', $data) ? (bool) $data['is_active'] : $shopProduct->is_active,
            ]);

            return $shopProduct->fresh(['product']);
        });
    }

    public function detachProduct(ShopProduct $shopProduct): bool
    {
        return DB::transaction(function () use ($shopProduct) {
            return (bool) $shopProduct->delete();
        });
    }
}

