<?php

namespace App\Domains\Nfse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceCatalogItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'max:5000'],
            'service_code' => ['required', 'string', 'max:10'],
            'national_tax_code' => ['nullable', 'string', 'max:30'],
            'municipal_tax_code' => ['nullable', 'string', 'max:60'],
            'nbs_code' => ['nullable', 'string', 'max:30'],
            'unit_price' => ['required', 'numeric', 'min:0'],
            'iss_rate' => ['required', 'numeric', 'min:0', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ];
    }
}
