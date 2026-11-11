<?php

namespace App\Http\Requests\ShopOwner;

use Illuminate\Foundation\Http\FormRequest;

class CreateCatalogProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->isShopOwner() ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'price' => ['required', 'numeric', 'min:0'],
            'thumbnail' => ['nullable', 'string', 'max:1000'],
        ];
    }
}

