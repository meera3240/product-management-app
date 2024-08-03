<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {
    // Admin
    Route::get('/admin/home', [HomeController::class, 'adminHome'])->name('admin.home');
    Route::get('products', [ProductController::class, 'index'])->name('products.index');
    Route::get('products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('products/store', [ProductController::class, 'store'])->name('products.store');
    Route::get('products/{encryptedId}', [ProductController::class, 'show'])->name('products.show');
    Route::get('products/{encryptedId}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');
    Route::post('products/import', [ProductController::class, 'import'])->name('products.import');
    Route::post('/products/bulk-delete', [ProductController::class, 'bulkDelete'])->name('products.bulkDelete');

    // SubAdmin
    Route::get('/sub-admin/home', [HomeController::class, 'subAdminHome'])->name('sub-admin.home');
});
