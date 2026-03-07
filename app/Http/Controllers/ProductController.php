<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class ProductController extends Controller
{
    public function index(Request $request): View
    {
        $products = Product::query()
            ->with('category')
            ->when($request->filled('category_id'), fn ($query) => $query->where('category_id', $request->integer('category_id')))
            ->orderBy('category_id')
            ->orderBy('name')
            ->get();

        $groupedProducts = $products->groupBy(fn (Product $product) => $product->category?->name ?? 'Sem categoria');
        $categories = Category::query()->orderBy('name')->get();

        return view('products.index', compact('products', 'groupedProducts', 'categories'));
    }

    public function show(Product $product): View
    {
        return view('products.show', [
            'product' => $product->load('category'),
        ]);
    }

    public function image(Product $product): BinaryFileResponse
    {
        if (!$product->image_path || !Storage::disk('public')->exists($product->image_path)) {
            abort(404);
        }

        return response()->file(Storage::disk('public')->path($product->image_path));
    }
}
