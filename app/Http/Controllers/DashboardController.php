<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Movement;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $totalProducts = Product::query()->count();
        $totalCategories = Category::query()->count();
        $lowStock = Product::query()->where('stock', '<=', 5)->count();
        $recentMovements = Movement::query()->with('product')->latest()->take(10)->get();

        return view('dashboard', compact('totalProducts', 'totalCategories', 'lowStock', 'recentMovements'));
    }
}
