<?php

namespace App\Http\Controllers\Api\Customer;

use App\Http\Controllers\Controller;
use App\Http\Resources\ShopProductResource;
use App\Http\Resources\ShopResource;
use App\Services\Contracts\ShopProductServiceInterface;
use App\Services\Contracts\ShopServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class PublicShopController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ShopServiceInterface $shopService,
        protected ShopProductServiceInterface $shopProductService
    ) {
    }

    public function shops(): JsonResponse
    {
        $shops = $this->shopService->getActiveShops();

        return $this->successResponse(ShopResource::collection($shops));
    }

    public function products(string $slug): JsonResponse
    {
        $shop = $this->shopService->findBySlug($slug);

        if (! $shop) {
            return $this->errorResponse('Shop not found.', 404);
        }

        $products = $this->shopProductService->getPublicShopProducts($shop);

        return $this->successResponse([
            'shop' => new ShopResource($shop),
            'products' => ShopProductResource::collection($products),
        ]);
    }

    public function productDetail(string $slug, int $id): JsonResponse
    {
        $shop = $this->shopService->findBySlug($slug);

        if (! $shop) {
            return $this->errorResponse('Shop not found.', 404);
        }

        $shopProduct = $this->shopProductService->getPublicShopProduct($shop, $id);

        if (! $shopProduct) {
            return $this->errorResponse('Product not found in this shop.', 404);
        }

        return $this->successResponse(new ShopProductResource($shopProduct));
    }
}

