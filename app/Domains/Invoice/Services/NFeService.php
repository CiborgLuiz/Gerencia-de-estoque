<?php

namespace App\Domains\Invoice\Services;

use App\Models\Invoice;
use RuntimeException;

class NFeService
{
    public function buildXml(Invoice $invoice): string
    {
        if (config('nfe.driver') === 'sped_nfe' && !class_exists(\NFePHP\NFe\Make::class)) {
            throw new RuntimeException('Pacote nfephp-org/sped-nfe não encontrado. Instale-o para usar NFE_DRIVER=sped_nfe.');
        }

        $payload = [
            'invoice_id' => $invoice->id,
            'total' => $invoice->total_value,
            'environment' => config('nfe.environment'),
            'cnpj' => config('nfe.cnpj'),
            'items' => $invoice->items->map(fn ($item) => [
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ])->all(),
        ];

        return '<nfe>'.json_encode($payload, JSON_UNESCAPED_UNICODE).'</nfe>';
    }

    public function signXml(string $xml): string
    {
        if (config('nfe.driver') === 'sped_nfe' && !class_exists(\NFePHP\NFe\Tools::class)) {
            throw new RuntimeException('Classe NFePHP\\NFe\\Tools não encontrada. Verifique a instalação do sped-nfe.');
        }

        // Fallback: em modo mock, apenas retorna o XML.
        // Em modo sped_nfe, a assinatura e autorização acontecem no SefazClient.
        return $xml;
    }
}
