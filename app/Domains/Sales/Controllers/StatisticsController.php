<?php

namespace App\Domains\Sales\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\SaleItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class StatisticsController extends Controller
{
    public function index(): View
    {
        $profit = SaleItem::query()->join('products', 'products.id', '=', 'sale_items.product_id')
            ->selectRaw('COALESCE(SUM((sale_items.unit_price - products.purchase_price) * sale_items.quantity),0) as total')
            ->value('total');

        $monthlyRevenue = DB::table('sales')
            ->selectRaw('strftime("%Y-%m", created_at) as month, SUM(total_value) as total')
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        return view('dashboard', [
            'totalProducts' => Product::count(),
            'totalCategories' => DB::table('categories')->count(),
            'lowStock' => Product::criticalStock()->count(),
            'recentMovements' => DB::table('movements')->latest()->limit(10)->get(),
            'profit' => $profit,
            'monthlyRevenue' => $monthlyRevenue,
            'topSeller' => User::query()->withCount('sales')->orderByDesc('sales_count')->first(),
            'topProducts' => SaleItem::query()
                ->select('product_id', DB::raw('SUM(quantity) as qty'))
                ->with('product')
                ->groupBy('product_id')
                ->orderByDesc('qty')
                ->limit(5)
                ->get(),
        ]);
    }
}
