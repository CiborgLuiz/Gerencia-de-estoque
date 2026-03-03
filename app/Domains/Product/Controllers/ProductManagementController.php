<?php

namespace App\Domains\Product\Controllers;

use App\Domains\Product\Requests\StoreProductRequest;
use App\Domains\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ProductManagementController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): View
    {
        $products = Product::with('category')->latest()->paginate(20);

        return view('products.manage', [
            'products' => $products,
            'categories' => Category::tree()->get(),
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->create($request->validated());

        return back()->with('status', 'Produto cadastrado com sucesso.');
    }
}
