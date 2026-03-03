<?php

use App\Domains\Invoice\Controllers\InvoiceController;
use App\Domains\Product\Controllers\ProductManagementController;
use App\Domains\Sales\Controllers\SaleController;
use App\Domains\Sales\Controllers\StatisticsController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome')->name('home');

require __DIR__.'/auth.php';

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [StatisticsController::class, 'index'])->name('dashboard');

    Route::controller(ProfileController::class)->group(function () {
        Route::get('/profile', 'edit')->name('profile.edit');
        Route::patch('/profile', 'update')->name('profile.update');
        Route::delete('/profile', 'destroy')->name('profile.destroy');
    });

    Route::get('/catalogo', [SaleController::class, 'catalog'])->name('sales.catalog');
    Route::post('/vendas', [SaleController::class, 'store'])->middleware('can:create,App\\Models\\Sale')->name('sales.store');

    Route::get('/produtos/gestao', [ProductManagementController::class, 'index'])
        ->middleware('can:viewAny,App\\Models\\Product')
        ->name('products.manage');

    Route::post('/produtos', [ProductManagementController::class, 'store'])
        ->middleware('can:create,App\\Models\\Product')
        ->name('products.store');

    Route::get('/notas-fiscais', [InvoiceController::class, 'index'])
        ->middleware('role:admin,gerente')
        ->name('invoices.index');

    Route::post('/notas-fiscais/{invoice}/cancelar', [InvoiceController::class, 'cancel'])
        ->middleware('role:admin')
        ->name('invoices.cancel');
});

Route::middleware(['auth', 'verified', 'is.admin'])
    ->prefix('admin')
    ->as('admin.')
    ->group(base_path('routes/admin.php'));
