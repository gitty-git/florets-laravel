<?php

use App\Http\Controllers\OrderController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\AdminProductsController;
use App\Http\Controllers\AttributesController;
use App\Http\Controllers\CurrentDateController;
use App\Http\Controllers\PaymentController;
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
Route::get('/attributes/{id}', [AttributesController::class, 'show']);
Route::post('paym_tinkoff', [PaymentController::class, 'handle']);
Route::get('order/{id}', [OrderController::class, 'show']);

Route::middleware(['auth:sanctum', 'isAdmin'])->group(function () {
    Route::prefix('admin')->group(function () {
        Route::get('products', [AdminProductsController::class, 'index']);
        Route::get('products/{product}', [AdminProductsController::class, 'show']);
        Route::post('products', [AdminProductsController::class, 'store']);
        Route::put('products/{product}', [AdminProductsController::class, 'update']);
        Route::delete('products/{product}', [AdminProductsController::class, 'destroy']);

        Route::post('attributes', [AttributesController::class, 'store']);        
        Route::put('attributes/{attribute}', [AttributesController::class, 'update']);
        Route::delete('attributes/{attribute}', [AttributesController::class, 'destroy']);

        Route::post('/image/add', [ImageController::class, 'store']);
        Route::post('/image/delete', [ImageController::class, 'destroy']);
    });
});

Route::post('order', [OrderController::class, 'store']);
Route::get('opening-hours', [OpeningHoursController::class, 'index']);
Route::get('get-date', [CurrentDateController::class, 'index']);

Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('orders/{option}', [OrderController::class, 'index']);    
    Route::put('orders/{order}', [OrderController::class, 'update']);
});