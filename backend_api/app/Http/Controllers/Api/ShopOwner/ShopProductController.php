<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\DTOs\ShopProductListingDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShopOwner\AttachShopProductRequest;
use App\Http\Requests\ShopOwner\UpdateShopProductRequest;
use App\Http\Resources\ShopProductResource;
use App\Models\ShopProduct;
use App\Services\Contracts\ShopProductServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ShopProductController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ShopProductServiceInterface $shopProductService
    ) {
    }

    public function index(Request $request): JsonResponse
    {
        $shopId = $request->user()->shop_id;
        $products = $this->shopProductService->getShopProducts($shopId);

        return $this->successResponse(ShopProductResource::collection($products));
    }

    public function attach(AttachShopProductRequest $request): JsonResponse
    {
        $dto = ShopProductListingDTO::fromArray(array_merge(
            $request->validated(),
            ['shop_id' => $request->user()->shop_id]
        ));

        $shopProduct = $this->shopProductService->attachOrUpdateProduct($dto);

        return $this->successResponse(new ShopProductResource($shopProduct), 'Product attached to shop successfully.', 201);
    }

    public function update(UpdateShopProductRequest $request, int $id): JsonResponse
    {
        $shopProduct = ShopProduct::where('id', $id)
            ->where('shop_id', $request->user()->shop_id)
            ->first();

        if (! $shopProduct) {
            return $this->errorResponse('Product not found in your shop.', 404);
        }

        $updated = $this->shopProductService->updateShopProduct($shopProduct, $request->validated());

        return $this->successResponse(new ShopProductResource($updated), 'Shop product updated successfully.');
    }

    public function destroy(Request $request, int $id): JsonResponse
    {
        $shopProduct = ShopProduct::where('id', $id)
            ->where('shop_id', $request->user()->shop_id)
            ->first();

        if (! $shopProduct) {
            return $this->errorResponse('Product not found in your shop.', 404);
        }

        $this->shopProductService->detachProduct($shopProduct);

        return $this->successResponse(null, 'Product removed from shop successfully.');
    }
}

