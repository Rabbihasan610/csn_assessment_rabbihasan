<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopProductResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $product = $this->product;

        $effectiveTitle = $this->custom_title ?: ($product ? $product->title : '');
        $effectiveDescription = $this->custom_description ?: ($product ? $product->description : '');
        $effectivePrice = $this->custom_price !== null ? (float) $this->custom_price : ($product ? (float) $product->price : 0.0);
        $effectiveThumbnail = $this->custom_thumbnail ?: ($product ? $product->thumbnail : null);

        return [
            'id' => $this->id,
            'shop_id' => $this->shop_id,
            'product_id' => $this->product_id,
            'title' => $effectiveTitle,
            'description' => $effectiveDescription,
            'price' => $effectivePrice,
            'thumbnail' => $effectiveThumbnail,
            'stock' => (int) $this->stock,
            'is_active' => (bool) $this->is_active,
            'custom_overrides' => [
                'custom_title' => $this->custom_title,
                'custom_description' => $this->custom_description,
                'custom_price' => $this->custom_price !== null ? (float) $this->custom_price : null,
                'custom_thumbnail' => $this->custom_thumbnail,
            ],
            'original_product' => new ProductResource($this->whenLoaded('product')),
            'created_at' => $this->created_at?->toISOString(),
            'updated_at' => $this->updated_at?->toISOString(),
        ];
    }
}

