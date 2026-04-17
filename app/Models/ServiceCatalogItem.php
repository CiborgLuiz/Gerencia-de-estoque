<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceCatalogItem extends Model
{
    protected $fillable = [
        'description',
        'long_description',
        'service_code',
        'national_tax_code',
        'municipal_tax_code',
        'nbs_code',
        'unit_price',
        'iss_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'iss_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function invoiceItems(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class);
    }
}
