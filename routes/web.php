<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/', fn() => redirect()->route('products.index'));

Route::get('/products', [ProductController::class, 'index'])->name('products.index');
Route::get('/products/{sku}', [ProductController::class, 'show'])->name('products.show');
