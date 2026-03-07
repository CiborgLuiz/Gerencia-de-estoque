<?php

namespace App\Domains\Product\Controllers;

use App\Domains\Product\Requests\StoreProductRequest;
use App\Domains\Product\Services\ProductService;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Throwable;

class ProductManagementController extends Controller
{
    public function __construct(private readonly ProductService $productService)
    {
    }

    public function index(): View
    {
        $products = Product::with('category')
            ->orderBy('category_id')
            ->orderBy('name')
            ->paginate(20);
        $categories = Category::query()->select('id', 'name', 'parent_id')->orderBy('name')->get();
        $categoryTree = $this->buildCategoryTree($categories);
        $flatCategories = $this->appendOrphans($this->flattenCategories($categoryTree), $categories);

        return view('products.manage', [
            'products' => $products,
            'categoryTree' => $categoryTree,
            'flatCategories' => $flatCategories,
        ]);
    }

    public function store(StoreProductRequest $request): RedirectResponse
    {
        try {
            $this->productService->create($request->validated());
        } catch (Throwable $exception) {
            return back()
                ->withInput()
                ->withErrors([
                    'product' => 'Erro ao salvar produto: '.$exception->getMessage(),
                ]);
        }

        return back()->with('status', 'Produto cadastrado com sucesso.');
    }

    private function buildCategoryTree(iterable $categories, ?int $parentId = null): array
    {
        $children = [];

        foreach ($categories as $category) {
            $currentParent = $category->parent_id !== null ? (int) $category->parent_id : null;
            if ($currentParent !== $parentId) {
                continue;
            }

            $node = [
                'id' => (int) $category->id,
                'name' => (string) $category->name,
                'children' => $this->buildCategoryTree($categories, (int) $category->id),
            ];

            $children[] = $node;
        }

        return $children;
    }

    private function flattenCategories(array $nodes, int $depth = 0): array
    {
        $flattened = [];

        foreach ($nodes as $node) {
            $flattened[] = [
                'id' => $node['id'],
                'name' => str_repeat('└─ ', $depth).$node['name'],
            ];

            if (!empty($node['children'])) {
                foreach ($this->flattenCategories($node['children'], $depth + 1) as $child) {
                    $flattened[] = $child;
                }
            }
        }

        return $flattened;
    }

    // Fallback para categorias orfas (parent_id invalido) que nao entram na arvore principal.
    private function appendOrphans(array $flattened, iterable $categories): array
    {
        $listedIds = array_column($flattened, 'id');

        foreach ($categories as $category) {
            if (!in_array((int) $category->id, $listedIds, true)) {
                $flattened[] = [
                    'id' => (int) $category->id,
                    'name' => '(orfã) '.$category->name,
                ];
            }
        }

        return $flattened;
    }
}
