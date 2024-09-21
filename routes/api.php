<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CartController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\VendorController;
use Illuminate\Support\Facades\Route;

Route::group(['middleware' => ['limit_requests:5,60'], 'prefix' => '/'], function () {
    Route::post('login', [AuthController::class, 'login'])->name('login');
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('verify.email');
    Route::patch('reset-password/{token}', [AuthController::class, 'resetPassword'])->name('reset.password');
});
Route::group(['middleware' => ['limit_requests:1,60'], 'prefix' => '/'], function () {
    Route::post('resend-verification-code', [AuthController::class, 'resendVerificationCode'])
        ->name('resend.verification.code');
    Route::post('forget-password', [AuthController::class, 'forgetPassword'])->name('forget.password');
});
Route::group(['middleware' => ['limit_requests', 'auth:sanctum']], function () {
    Route::post('refresh-token', [AuthController::class, 'refreshToken'])->name('refresh.token');
    Route::post('logout', [AuthController::class, 'logout'])->name('logout');

    Route::apiResource('categories', CategoryController::class);
    Route::name('subcategories.')->prefix('categories')->group(function () {
        Route::patch('/{category}/move-subcategories', [CategoryController::class, 'moveSubcategories'])
            ->name('parent');
        Route::patch('/subcategories/remove-parent', [CategoryController::class, 'removeSubcategoriesParent'])
            ->name('parent.null');
    });

    Route::apiResource('coupons', CouponController::class);
    Route::apiResource('products', ProductController::class);
    Route::apiResource('users', UserController::class);
    Route::apiResource('carts', CartController::class);
    Route::apiResource('vendors', VendorController::class);
});

