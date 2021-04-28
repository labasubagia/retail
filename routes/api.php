<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\ProductTypeController;
use App\Http\Controllers\Api\VendorController;
use App\Http\Controllers\Api\EnterpriseController;
use App\Http\Controllers\Api\StoreController;
use App\Http\Controllers\Api\StoreStockController;
use App\Http\Controllers\Api\OrderController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::group(['prefix' =>'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
});

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::group(['prefix' => 'user'], function() {
        Route::get('', [UserController::class, 'current']);
    });
    Route::apiResource('enterprise', EnterpriseController::class);
    Route::apiResource('brand', BrandController::class);
    Route::apiResource('product', ProductController::class);
    Route::apiResource('product-type', ProductTypeController::class);
    Route::apiResource('vendor', VendorController::class);
    Route::apiResource('store', StoreController::class);
    Route::group(['prefix' => 'store-stock'], function() {
        Route::post('/{store_stock?}', [StoreStockController::class, 'upsert']);
    });
    Route::apiResource('order', OrderController::class)->only('index', 'show', 'store');
});

