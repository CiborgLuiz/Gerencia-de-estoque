<?php

use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'admin.dashboard')->name('dashboard');

Route::resource('products', ProductController::class)->only(['index']);

Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');
