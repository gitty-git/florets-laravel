<?php

use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\AdminProductsController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\OpeningHoursController;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();    
});

// Route::middleware('auth:sanctum')->group(function () {
    
// });

// Route::resource('product', ProductController::class);
Route::get('products', [ProductsController::class, 'index']);
Route::get('/products/{search}', [ProductsController::class, 'search']);



// Route::get('products/{id}', [ProductsController::class, 'show']);

Route::prefix('admin')->group(function () {
    Route::get('products', [AdminProductsController::class, 'index']);
    Route::post('products', [AdminProductsController::class, 'store']);
    Route::put('products/{product}', [AdminProductsController::class, 'update']);

    Route::post('/image/add', [ImageController::class, 'store']);
    Route::post('/image/delete', [ImageController::class, 'delete']);
});

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    
});

Route::post('order', [OrderController::class, 'store']);
Route::get('opening-hours', [OpeningHoursController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('orders/{option}', [OrderController::class, 'index']);
    Route::get('order/{id}', [OrderController::class, 'show']);
    Route::put('orders/{order}', [OrderController::class, 'update']);
});