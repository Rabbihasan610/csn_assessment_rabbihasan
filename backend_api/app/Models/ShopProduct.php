<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShopProduct extends Model
{
    use HasFactory;

    protected $table = 'shop_products';

    protected $fillable = [
        'shop_id',
        'product_id',
        'custom_title',
        'custom_description',
        'custom_price',
        'custom_thumbnail',
        'stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'custom_price' => 'decimal:2',
            'stock' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function shop(): BelongsTo
    {
        return $this->belongsTo(Shop::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function getEffectiveTitleAttribute(): string
    {
        return $this->custom_title ?: ($this->product ? $this->product->title : '');
    }

    public function getEffectiveDescriptionAttribute(): ?string
    {
        return $this->custom_description ?: ($this->product ? $this->product->description : null);
    }

    public function getEffectivePriceAttribute(): string
    {
        return (string) ($this->custom_price !== null ? $this->custom_price : ($this->product ? $this->product->price : '0.00'));
    }

    public function getEffectiveThumbnailAttribute(): ?string
    {
        return $this->custom_thumbnail ?: ($this->product ? $this->product->thumbnail : null);
    }
}

