<?php

use App\Http\Controllers\ProductController;
use Illuminate\Support\Facades\Route;


Route::controller(ProductController::class)->group(function () {
    Route::get('/', 'index');
    Route::resource('/products', ProductController::class);
    Route::get('/trendyol/products', 'fetchProducts')->name('fetch_product');
});
