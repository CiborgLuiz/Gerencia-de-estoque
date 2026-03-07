<?php

namespace App\Domains\Sales\Controllers;

use App\Domains\Sales\Requests\StoreSaleRequest;
use App\Domains\Sales\Services\SaleService;
use App\Models\Category;
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
        $products = Product::query()
            ->with('category')
            ->where('status', 'ativo')
            ->when(
                $request->filled('search'),
                fn ($query) => $query->where(function ($subQuery) use ($request) {
                    $term = trim((string) $request->input('search'));
                    $subQuery
                        ->where('name', 'like', "%{$term}%")
                        ->orWhere('internal_code', 'like', "%{$term}%");
                })
            )
            ->when(
                $request->filled('category_id'),
                fn ($query) => $query->where('category_id', $request->integer('category_id'))
            )
            ->when(
                $request->input('stock_filter') === 'low',
                fn ($query) => $query->whereColumn('stock', '<=', 'minimum_stock')
            )
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $groupedProducts = $products->groupBy(fn (Product $product) => $product->category?->name ?? 'Sem categoria');
        $categories = Category::query()->orderBy('name')->get(['id', 'name']);

        return view('sales.catalog', [
            'products' => $products,
            'groupedProducts' => $groupedProducts,
            'categories' => $categories,
            'search' => (string) $request->input('search', ''),
            'selectedCategoryId' => $request->filled('category_id') ? (string) $request->input('category_id') : '',
            'selectedStockFilter' => (string) $request->input('stock_filter', ''),
        ]);
    }

    public function product(Product $product)
    {
        return view('sales.product', ['product' => $product->load('category')]);
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

        return response()->json([
            'message' => 'Venda registrada com sucesso.',
            'sale_id' => $sale->id,
            'invoice_id' => $sale->invoice?->id,
            'invoice_url' => $sale->invoice ? route('invoices.show', $sale->invoice) : null,
        ], 201);
    }
}
