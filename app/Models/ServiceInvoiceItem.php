<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceInvoiceItem extends Model
{
    protected $fillable = [
        'service_invoice_id',
        'service_catalog_item_id',
        'description',
        'long_description',
        'service_code',
        'national_tax_code',
        'municipal_tax_code',
        'nbs_code',
        'quantity',
        'unit_price',
        'total_price',
        'iss_rate',
    ];

    protected function casts(): array
    {
        return [
            'unit_price' => 'decimal:2',
            'total_price' => 'decimal:2',
            'iss_rate' => 'decimal:2',
        ];
    }

    public function serviceInvoice(): BelongsTo
    {
        return $this->belongsTo(ServiceInvoice::class);
    }

    public function serviceCatalogItem(): BelongsTo
    {
        return $this->belongsTo(ServiceCatalogItem::class);
    }
}
