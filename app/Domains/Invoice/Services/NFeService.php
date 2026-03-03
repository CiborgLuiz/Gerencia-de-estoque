<?php

namespace App\Domains\Invoice\Services;

use App\Models\Invoice;

class NFeService
{
    public function buildXml(Invoice $invoice): string
    {
        $payload = [
            'invoice_id' => $invoice->id,
            'total' => $invoice->total_value,
            'items' => $invoice->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])->all(),
        ];

        return '<nfe>'.e(json_encode($payload, JSON_UNESCAPED_UNICODE)).'</nfe>';
    }

    public function signXml(string $xml): string
    {
        // Em produção: utilizar certificado A1 (.pfx) com biblioteca NFePHP.
        return $xml;
    }
}
