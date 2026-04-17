<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ServiceInvoice extends Model
{
    protected $fillable = [
        'user_id',
        'customer_id',
        'total_value',
        'total_tax',
        'status',
        'number',
        'rps_number',
        'protocol',
        'verification_code',
        'xml',
        'response_payload',
        'customer_data',
        'service_context',
        'issued_at',
    ];

    protected function casts(): array
    {
        return [
            'total_value' => 'decimal:2',
            'total_tax' => 'decimal:2',
            'response_payload' => 'array',
            'customer_data' => 'array',
            'service_context' => 'array',
            'issued_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(ServiceInvoiceItem::class);
    }
}
