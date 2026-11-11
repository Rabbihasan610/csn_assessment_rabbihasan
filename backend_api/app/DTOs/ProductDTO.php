<?php

namespace App\DTOs;

class ProductDTO extends BaseDTO
{
    public function __construct(
        public readonly string $title,
        public readonly ?string $description,
        public readonly float $price,
        public readonly ?string $thumbnail = null,
        public readonly ?int $createdBy = null
    ) {
    }

    public static function fromArray(array $data): static
    {
        return new static(
            title: $data['title'],
            description: $data['description'] ?? null,
            price: (float) $data['price'],
            thumbnail: $data['thumbnail'] ?? null,
            createdBy: isset($data['created_by']) ? (int) $data['created_by'] : null
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'title' => $this->title,
            'description' => $this->description,
            'price' => $this->price,
            'thumbnail' => $this->thumbnail,
            'created_by' => $this->createdBy,
        ], fn ($value) => $value !== null);
    }
}

