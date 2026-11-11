<?php

namespace App\Http\Controllers\Api\ShopOwner;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\ShopOwner\CreateCatalogProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class CatalogController extends Controller
{
    use ApiResponseTrait;

    public function __construct(
        protected ProductServiceInterface $productService
    ) {
    }

    public function index(): JsonResponse
    {
        $products = $this->productService->getMasterCatalog();

        return $this->successResponse(ProductResource::collection($products));
    }

    public function store(CreateCatalogProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromArray(array_merge(
            $request->validated(),
            ['created_by' => $request->user()->id]
        ));

        $product = $this->productService->createProduct($dto);

        return $this->successResponse(new ProductResource($product->load('creator')), 'Product added to global catalog.', 201);
    }
}

