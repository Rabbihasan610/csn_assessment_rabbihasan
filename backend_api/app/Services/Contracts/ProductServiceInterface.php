<?php

namespace App\Services\Contracts;

use App\DTOs\ProductDTO;
use App\Models\Product;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface ProductServiceInterface
{
    public function getAllProducts(int $perPage = 15): LengthAwarePaginator;

    public function getMasterCatalog(): Collection;

    public function findById(int $id): ?Product;

    public function createProduct(ProductDTO $dto): Product;

    public function updateProduct(Product $product, ProductDTO $dto): Product;

    public function deleteProduct(Product $product): bool;
}

