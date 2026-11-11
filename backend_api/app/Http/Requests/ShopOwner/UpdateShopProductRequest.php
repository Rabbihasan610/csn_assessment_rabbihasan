<?php

namespace App\Http\Requests\ShopOwner;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShopProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isShopOwner() && $this->user()?->shop_id !== null;
    }

    public function rules(): array
    {
        return [
            'custom_title' => ['nullable', 'string', 'max:255'],
            'custom_description' => ['nullable', 'string'],
            'custom_price' => ['nullable', 'numeric', 'min:0'],
            'custom_thumbnail' => ['nullable', 'string', 'max:1000'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}

