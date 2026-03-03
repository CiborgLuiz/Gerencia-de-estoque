<?php

namespace App\Providers;

use App\Domains\Product\Policies\ProductPolicy;
use App\Domains\Sales\Policies\SalePolicy;
use App\Models\Product;
use App\Models\Sale;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::policy(Product::class, ProductPolicy::class);
        Gate::policy(Sale::class, SalePolicy::class);
    }
}
