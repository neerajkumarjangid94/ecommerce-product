<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ColorController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\SizeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (v1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {

    // Admin- login api after seeding.
    Route::post('/login', [AuthController::class, 'login']);

    // user's products apis (Public without auth.)
    Route::prefix('user/products')->controller(ProductController::class)->group(function () {
        Route::get('/', 'productListing');                          // Listing of products
        Route::get('/{id}', 'productDetails')->whereNumber('id');    // Product details by ID
    });

    // All Colors & Sizes Listings of products they added by admin. (Public)
    Route::get('/colors', [ColorController::class, 'colorslisting']);
    Route::get('/colors/{color}', [ColorController::class, 'colorsDetailsbyid']);

    Route::get('/sizes', [SizeController::class, 'sizesListing']);
    Route::get('/sizes/{size}', [SizeController::class, 'sizeDetailsByid']);


    // PROTECTED ROUTES (auth:sanctum)
    Route::middleware('auth:sanctum')->group(function () {

        // Auth management
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::get('/user', [AuthController::class, 'selfProfile']);

        // Product Management CRUD
        Route::prefix('products')->controller(ProductController::class)->group(function () {
            Route::get('/all', 'adminProductListing'); // Admin listing with all products
            Route::post('/', 'ProductCreate');
            Route::match(['put', 'post'], '/{id}', 'update');
            Route::delete('/{id}', 'destroy');
        });

        // Color Management
        Route::prefix('colors')->controller(ColorController::class)->group(function () {
            Route::post('/', 'store');
            Route::delete('/{color}', 'destroy');
        });

        // Size Management
        Route::prefix('sizes')->controller(SizeController::class)->group(function () {
            Route::post('/', 'createSizes');
            Route::delete('/{size}', 'destroy');
        });
    });
});
