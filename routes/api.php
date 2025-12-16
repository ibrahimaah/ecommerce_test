<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ProductController;

/*
|--------------------------------------------------------------------------
| API Authentication Routes
|--------------------------------------------------------------------------
|
| Public routes: register, login
| Protected routes: logout (requires authentication)
|
*/

Route::prefix('auth')->name('auth.')->group(function () {

    // Public endpoints
    Route::post('register', [AuthController::class, 'register'])->name('register');
    Route::post('login', [AuthController::class, 'login'])->name('login');

    // Protected endpoints
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('logout', [AuthController::class, 'logout'])->name('logout');
    });

});

/*
|--------------------------------------------------------------------------
| API Product Routes
|--------------------------------------------------------------------------
|
| Public endpoints: list and show products
| Protected endpoints: create, update, delete (requires authentication)
|
*/

Route::prefix('products')->name('products.')->group(function () {

    // Public endpoints
    Route::get('/', [ProductController::class, 'index'])->name('index');
    Route::get('{id}', [ProductController::class, 'show'])->name('show');

    // Protected endpoints (requires auth)
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/', [ProductController::class, 'store'])->name('store');
        Route::put('{id}', [ProductController::class, 'update'])->name('update');
        Route::delete('{id}', [ProductController::class, 'destroy'])->name('destroy');
    });

});
