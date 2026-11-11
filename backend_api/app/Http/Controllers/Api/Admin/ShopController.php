<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreShopRequest;
use App\Http\Requests\Admin\UpdateShopRequest;
use App\Http\Resources\ShopResource;
use App\Services\Contracts\ShopServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class ShopController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ShopServiceInterface $shopService
    ) {
    }

    public function index(): JsonResponse
    {
        $shops = $this->shopService->getAllShops();

        return $this->successResponse(ShopResource::collection($shops));
    }

    public function store(StoreShopRequest $request): JsonResponse
    {
        $shop = $this->shopService->createShop($request->validated());

        return $this->successResponse(new ShopResource($shop), 'Shop created successfully.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $shop = $this->shopService->findById($id);

        if (! $shop) {
            return $this->errorResponse('Shop not found.', 404);
        }

        return $this->successResponse(new ShopResource($shop));
    }

    public function update(UpdateShopRequest $request, int $id): JsonResponse
    {
        $shop = $this->shopService->findById($id);

        if (! $shop) {
            return $this->errorResponse('Shop not found.', 404);
        }

        $updated = $this->shopService->updateShop($shop, $request->validated());

        return $this->successResponse(new ShopResource($updated), 'Shop updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $shop = $this->shopService->findById($id);

        if (! $shop) {
            return $this->errorResponse('Shop not found.', 404);
        }

        $this->shopService->deleteShop($shop);

        return $this->successResponse(null, 'Shop deleted successfully.');
    }
}

