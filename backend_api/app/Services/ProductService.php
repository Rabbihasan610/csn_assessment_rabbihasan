<?php

namespace App\Services;

use App\DTOs\ProductDTO;
use App\Models\Product;
use App\Services\Contracts\ProductServiceInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductService implements ProductServiceInterface
{
    public function getAllProducts(int $perPage = 15): LengthAwarePaginator
    {
        return Product::with('creator')->latest()->paginate($perPage);
    }

    public function getMasterCatalog(): Collection
    {
        return Product::with('creator')->latest()->get();
    }

    public function findById(int $id): ?Product
    {
        return Product::with('creator')->find($id);
    }

    public function createProduct(ProductDTO $dto): Product
    {
        return DB::transaction(function () use ($dto) {
            $data = $dto->toArray();
            $data['slug'] = $this->generateUniqueSlug($dto->title);

            return Product::create($data);
        });
    }

    public function updateProduct(Product $product, ProductDTO $dto): Product
    {
        return DB::transaction(function () use ($product, $dto) {
            $data = $dto->toArray();

            if ($product->title !== $dto->title) {
                $data['slug'] = $this->generateUniqueSlug($dto->title, $product->id);
            }

            $product->update($data);

            return $product->fresh(['creator']);
        });
    }

    public function deleteProduct(Product $product): bool
    {
        return DB::transaction(function () use ($product) {
            return (bool) $product->delete();
        });
    }

    private function generateUniqueSlug(string $title, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($title);
        $slug = $baseSlug;
        $counter = 1;

        while (Product::where('slug', $slug)->when($ignoreId, fn ($q) => $q->where('id', '!=', $ignoreId))->exists()) {
            $slug = "{$baseSlug}-{$counter}";
            $counter++;
        }

        return $slug;
    }
}

