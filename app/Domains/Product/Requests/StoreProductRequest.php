<?php

namespace App\Domains\Product\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'internal_code' => ['required', 'string', 'max:100', Rule::unique('products', 'internal_code')],
            'manufacturer_code' => ['nullable', 'string', 'max:100'],
            'ncm' => ['required', 'string', 'max:10'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'image', 'max:6144', 'mimes:jpg,jpeg,png,webp,gif'],
            'purchase_price' => ['required', 'numeric', 'min:0'],
            'sale_price' => ['required', 'numeric', 'min:0'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'status' => ['required', Rule::in(['ativo', 'inativo'])],
            'category_id' => ['required', 'exists:categories,id'],
            'cfop' => ['nullable', 'string', 'max:4'],
            'cst_csosn' => ['nullable', 'string', 'max:4'],
            'origin' => ['nullable', 'string', 'max:2'],
            'icms_rate' => ['nullable', 'numeric', 'min:0'],
            'pis_rate' => ['nullable', 'numeric', 'min:0'],
            'cofins_rate' => ['nullable', 'numeric', 'min:0'],
        ];
    }
}
