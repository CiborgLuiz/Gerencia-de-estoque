<?php

namespace App\Domains\Invoice\Services;

use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\InvoiceLog;
use App\Models\Sale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class InvoiceService
{
    public function __construct(
        private readonly NFeService $nfeService,
        private readonly SefazClient $sefazClient,
    ) {
    }

    public function issueFromSale(Sale $sale): Invoice
    {
        return DB::transaction(function () use ($sale): Invoice {
            $invoice = Invoice::create([
                'user_id' => $sale->user_id,
                'customer_id' => $sale->customer_id,
                'sale_id' => $sale->id,
                'total_value' => $sale->total_value,
                'total_tax' => 0,
                'status' => 'rejeitada',
            ]);

            foreach ($sale->items as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_price' => $item->total_price,
                ]);
            }

            $invoice->load('items');

            $xml = $this->nfeService->buildXml($invoice);
            $signedXml = $this->nfeService->signXml($xml);
            $response = $this->sefazClient->authorize($signedXml);

            $invoice->fill([
                'status' => $response['status'],
                'protocol' => $response['protocol'] ?? null,
                'chave_acesso' => $response['chave_acesso'] ?? null,
                'xml' => $response['xml'] ?? $signedXml,
                'authorized_at' => $response['status'] === 'autorizada' ? now() : null,
            ])->save();

            Storage::disk('local')->put('nfe/'.$invoice->id.'.xml', (string) $invoice->xml);

            InvoiceLog::create([
                'invoice_id' => $invoice->id,
                'operation' => 'autorizar',
                'status' => $invoice->status,
                'request_payload' => $signedXml,
                'response_payload' => json_encode($response, JSON_UNESCAPED_UNICODE),
                'message' => 'Processamento de autorização NF-e.',
            ]);

            return $invoice;
        });
    }

    public function cancel(Invoice $invoice, string $justification): Invoice
    {
        $response = $this->sefazClient->cancel((string) $invoice->chave_acesso, $justification);

        $invoice->update([
            'status' => $response['status'],
            'protocol' => $response['protocol'] ?? $invoice->protocol,
        ]);

        InvoiceLog::create([
            'invoice_id' => $invoice->id,
            'operation' => 'cancelar',
            'status' => $invoice->status,
            'request_payload' => $justification,
            'response_payload' => json_encode($response, JSON_UNESCAPED_UNICODE),
            'message' => 'Cancelamento de NF-e.',
        ]);

        return $invoice;
    }
}
