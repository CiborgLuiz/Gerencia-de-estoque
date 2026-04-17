<?php

namespace App\Domains\Nfse\Requests;

use Illuminate\Foundation\Http\FormRequest;

class IssueServiceInvoiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'service_catalog_item_id' => ['required', 'exists:service_catalog_items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'override_description' => ['nullable', 'string', 'max:5000'],
            'competence_date' => ['nullable', 'date'],
            'service_country' => ['nullable', 'string', 'max:60'],
            'service_city_name' => ['nullable', 'string', 'max:120'],
            'service_city_code' => ['nullable', 'digits:7'],
            'service_state' => ['nullable', 'string', 'size:2'],
            'national_tax_code' => ['nullable', 'string', 'max:30'],
            'nbs_code' => ['nullable', 'string', 'max:30'],
            'customer_name' => ['required', 'string', 'max:255'],
            'customer_document' => ['nullable', 'string', 'max:20'],
            'customer_email' => ['nullable', 'email', 'max:255'],
            'customer_phone' => ['nullable', 'string', 'max:30'],
            'customer_address' => ['nullable', 'string', 'max:255'],
            'customer_number' => ['nullable', 'string', 'max:30'],
            'customer_complement' => ['nullable', 'string', 'max:120'],
            'customer_neighborhood' => ['nullable', 'string', 'max:120'],
            'customer_city_code' => ['nullable', 'digits:7'],
            'customer_state' => ['nullable', 'string', 'size:2'],
            'customer_zip_code' => ['nullable', 'string', 'max:10'],
        ];
    }
}
