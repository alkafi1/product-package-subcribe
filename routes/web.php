<?php

use App\Http\Controllers\ProductController;
use App\Models\Product;
use Illuminate\Support\Facades\Route;


Route::get('/', [ProductController::class, 'home'])->name('home');
Route::resource('products', ProductController::class);

Route::post('/order', [ProductController::class, 'order'])->name('products.order');
//add cart
Route::post('/add-cart', [ProductController::class, 'addCart'])->name('products.cart');
Route::get('/add-cart', [ProductController::class, 'cartShow'])->name('products.cart.show');
Route::get('/delete-cart/{id}', [ProductController::class, 'cartDelete'])->name('products.cart.delete');
Route::get('/update-cart/{id}', [ProductController::class, 'cartUpdate'])->name('products.cart.update');
Route::post('/product-bundle-details', [ProductController::class, 'productBundleDetails'])->name('products.bundle.details');
Route::post('/cart-bundle-details', [ProductController::class, 'cartBundleDetails'])->name('cart.bundle.details');
Route::post('/cart-schedule-details', [ProductController::class, 'cartScheduleDetails'])->name('cart.schedule.details');

