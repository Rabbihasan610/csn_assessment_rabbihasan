<?php

use App\Http\Controllers\Api\Admin\ProductController as AdminProductController;
use App\Http\Controllers\Api\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Api\Admin\ShopOwnerController as AdminShopOwnerController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Customer\PublicShopController;
use App\Http\Controllers\Api\ShopOwner\CatalogController as OwnerCatalogController;
use App\Http\Controllers\Api\ShopOwner\ShopProductController as OwnerShopProductController;
use Illuminate\Support\Facades\Route;


// Authentication route
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/me', [AuthController::class, 'me']);
    });
});


// Admin route
Route::prefix('admin')->middleware(['auth:sanctum', 'admin'])->group(function () {
    Route::get('/shops', [AdminShopController::class, 'index']);
    Route::post('/shops', [AdminShopController::class, 'store']);
    Route::get('/shops/{id}', [AdminShopController::class, 'show']);
    Route::put('/shops/{id}', [AdminShopController::class, 'update']);
    Route::delete('/shops/{id}', [AdminShopController::class, 'destroy']);

    Route::get('/shop-owners', [AdminShopOwnerController::class, 'index']);
    Route::post('/shop-owners', [AdminShopOwnerController::class, 'store']);

    Route::get('/products', [AdminProductController::class, 'index']);
    Route::post('/products', [AdminProductController::class, 'store']);
    Route::get('/products/{id}', [AdminProductController::class, 'show']);
    Route::put('/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('/products/{id}', [AdminProductController::class, 'destroy']);
});


// Shop owner route
Route::prefix('shop-owner')->middleware(['auth:sanctum', 'shop_owner'])->group(function () {
    Route::get('/catalog', [OwnerCatalogController::class, 'index']);
    Route::post('/catalog', [OwnerCatalogController::class, 'store']);

    Route::get('/my-shop/products', [OwnerShopProductController::class, 'index']);
    Route::post('/my-shop/products/attach', [OwnerShopProductController::class, 'attach']);
    Route::put('/my-shop/products/{id}', [OwnerShopProductController::class, 'update']);
    Route::delete('/my-shop/products/{id}', [OwnerShopProductController::class, 'destroy']);
});


// Public route
Route::prefix('public')->group(function () {
    Route::get('/shops', [PublicShopController::class, 'shops']);
    Route::get('/shops/{slug}/products', [PublicShopController::class, 'products']);
    Route::get('/shops/{slug}/products/{id}', [PublicShopController::class, 'productDetail']);
});
