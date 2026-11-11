<?php

namespace App\Http\Controllers\Api\Admin;

use App\DTOs\ProductDTO;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreProductRequest;
use App\Http\Resources\ProductResource;
use App\Services\Contracts\ProductServiceInterface;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
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

    public function store(StoreProductRequest $request): JsonResponse
    {
        $dto = ProductDTO::fromArray(array_merge(
            $request->validated(),
            ['created_by' => $request->user()->id]
        ));

        $product = $this->productService->createProduct($dto);

        return $this->successResponse(new ProductResource($product->load('creator')), 'Product created successfully.', 201);
    }

    public function show(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (! $product) {
            return $this->errorResponse('Product not found.', 404);
        }

        return $this->successResponse(new ProductResource($product));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (! $product) {
            return $this->errorResponse('Product not found.', 404);
        }

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'thumbnail' => ['nullable', 'string', 'max:1000'],
        ]);

        $dto = ProductDTO::fromArray($validated);
        $updated = $this->productService->updateProduct($product, $dto);

        return $this->successResponse(new ProductResource($updated), 'Product updated successfully.');
    }

    public function destroy(int $id): JsonResponse
    {
        $product = $this->productService->findById($id);

        if (! $product) {
            return $this->errorResponse('Product not found.', 404);
        }

        $this->productService->deleteProduct($product);

        return $this->successResponse(null, 'Product deleted successfully.');
    }
}

