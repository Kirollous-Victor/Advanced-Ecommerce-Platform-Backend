<?php

use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['limit_requests']], function () {
    Route::apiResource('categories', CategoryController::class);
    Route::group(['prefix' => 'categories'], function () {
        Route::patch('/{category}/move-subcategories', [CategoryController::class, 'moveSubcategories']);
        Route::patch('/subcategories/remove-parent', [CategoryController::class, 'removeSubcategoriesParent']);
    });

    Route::apiResource('coupons', CouponController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('carts', CartController::class);
    Route::apiResource('vendors', VendorController::class);
});

