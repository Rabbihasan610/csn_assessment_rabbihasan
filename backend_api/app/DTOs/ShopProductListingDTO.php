<?php

namespace App\DTOs;

class ShopProductListingDTO extends BaseDTO
{
    public function __construct(
        public readonly int $productId,
        public readonly int $shopId,
        public readonly ?string $customTitle = null,
        public readonly ?string $customDescription = null,
        public readonly ?float $customPrice = null,
        public readonly ?string $customThumbnail = null,
        public readonly int $stock = 0,
        public readonly bool $isActive = true
    ) {
    }

    // create dto from array and return dto

    public static function fromArray(array $data): static
    {
        return new static(
            productId: (int) $data['product_id'],
            shopId: (int) $data['shop_id'],
            customTitle: $data['custom_title'] ?? null,
            customDescription: $data['custom_description'] ?? null,
            customPrice: isset($data['custom_price']) && $data['custom_price'] !== null && $data['custom_price'] !== '' ? (float) $data['custom_price'] : null,
            customThumbnail: $data['custom_thumbnail'] ?? null,
            stock: isset($data['stock']) ? (int) $data['stock'] : 0,
            isActive: isset($data['is_active']) ? (bool) $data['is_active'] : true
        );
    }

    // return properties as array

    public function toArray(): array
    {
        return [
            'product_id' => $this->productId,
            'shop_id' => $this->shopId,
            'custom_title' => $this->customTitle,
            'custom_description' => $this->customDescription,
            'custom_price' => $this->customPrice,
            'custom_thumbnail' => $this->customThumbnail,
            'stock' => $this->stock,
            'is_active' => $this->isActive,
        ];
    }
}

