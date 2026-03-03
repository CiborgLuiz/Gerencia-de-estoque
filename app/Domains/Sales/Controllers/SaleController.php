<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Sales\Requests\StoreSaleRequest;
use App\Domains\Sales\Services\SaleService;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use RuntimeException;
use Throwable;

class SaleController extends Controller
{
    public function __construct(private readonly SaleService $saleService)
    {
    }

    public function catalog(Request $request)
    {
        $products = Product::query()->with('category')->latest()->paginate(24);

        return view('sales.catalog', ['products' => $products]);
    }

    public function store(StoreSaleRequest $request): JsonResponse
    {
        try {
            $sale = $this->saleService->createSale($request->user(), $request->validated());
        } catch (RuntimeException $exception) {
            return response()->json(['message' => $exception->getMessage()], 422);
        } catch (Throwable) {
            return response()->json(['message' => 'Erro interno ao finalizar venda.'], 500);
        }

        return response()->json(['message' => 'Venda registrada com sucesso.', 'sale_id' => $sale->id], 201);
    }
}
