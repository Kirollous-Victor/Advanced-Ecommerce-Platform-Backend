<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\CouponController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('categories', CategoryController::class);
Route::patch('/categories/{category}/move-subcategories', [CategoryController::class, 'moveSubcategories']);

Route::apiResource('coupons', CouponController::class);
