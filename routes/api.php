<?php

use Illuminate\Http\Request;
use App\Http\Controllers\Api\CategoryController;
use Illuminate\Support\Facades\Route;

//Route::get('/user', function (Request $request) {
//    return $request->user();
//})->middleware('auth:sanctum');

Route::apiResource('category', CategoryController::class);
