<?php

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;


Route::get('/', [ProductController::class, 'home'])->name('home');
Route::resource('products', ProductController::class);
Route::post('/order', [ProductController::class, 'order'])->name('products.order');
