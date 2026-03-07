<?php

use App\Http\Controllers\AccessKeyController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\MovementController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'admin.dashboard')->name('dashboard');

Route::resource('products', ProductController::class)->only(['index', 'show']);

Route::get('/movements', [MovementController::class, 'index'])->name('movements.index');
Route::post('/movements', [MovementController::class, 'store'])->name('movements.store');

Route::middleware('role:dono,admin')->group(function () {
    Route::get('/chaves-acesso', [AccessKeyController::class, 'index'])->name('access-keys.index');
    Route::post('/chaves-acesso', [AccessKeyController::class, 'store'])->name('access-keys.store');
    Route::post('/chaves-acesso/{accessKey}/desligar', [AccessKeyController::class, 'revoke'])->name('access-keys.revoke');
});

Route::middleware('role:dono')->group(function () {
    Route::get('/funcionarios', [EmployeeController::class, 'index'])->name('employees.index');
    Route::delete('/funcionarios/{user}', [EmployeeController::class, 'destroy'])->name('employees.destroy');
});
