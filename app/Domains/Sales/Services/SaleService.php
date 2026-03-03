<?php

namespace App\Domains\Sales\Services;

use App\Domains\Invoice\Services\InvoiceService;
use App\Models\Product;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SaleService
{
    public function __construct(private readonly InvoiceService $invoiceService)
    {
    }

    public function createSale(User $user, array $payload): Sale
    {
        return DB::transaction(function () use ($user, $payload): Sale {
            $sale = Sale::create([
                'user_id' => $user->id,
                'customer_id' => $payload['customer_id'] ?? null,
                'total_value' => 0,
            ]);

            $total = 0;

            foreach ($payload['items'] as $item) {
                /** @var Product $product */
                $product = Product::query()->lockForUpdate()->findOrFail($item['product_id']);

                if ($product->stock < $item['quantity']) {
                    throw new RuntimeException("Estoque insuficiente para {$product->name}.");
                }

                $product->decrement('stock', $item['quantity']);

                $lineTotal = bcmul((string) $product->sale_price, (string) $item['quantity'], 2);
                $total = bcadd((string) $total, $lineTotal, 2);

                SaleItem::create([
                    'sale_id' => $sale->id,
                    'product_id' => $product->id,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->sale_price,
                    'total_price' => $lineTotal,
                ]);
            }

            $sale->update(['total_value' => $total]);
            $sale->load('items');

            $this->invoiceService->issueFromSale($sale);

            return $sale->fresh(['items.product', 'user', 'customer']);
        });
    }
}
