<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;

require __DIR__ . '/auth.php';

//user's route.
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [HomeController::class, 'show'])->name('product.show');



Route::middleware('auth')->group(function () {
    //dashboard route.
    Route::get('/dashboard', [AdminController::class, 'admin_product'])
        ->middleware(['auth', 'verified'])
        ->name('dashboard');

    //profile routes for user profile management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');


    //Admin route after login.
    Route::prefix('admin')
        ->controller(AdminController::class)
        ->group(function () {
            Route::get('/product', 'admin_product')->name('admin.product'); //Admin product list route after login.
            Route::get('/new-product', 'admin_newproduct')->name('admin.newproduct');  //new product view page.
        });

    // Product CRUD routes
    Route::prefix('admin/product')
        ->name('admin.product.')
        ->controller(ProductController::class)
        ->group(function () {
            Route::post('/', 'store')->name('store');
            Route::get('/{product}/edit', 'edit')->name('edit');
            Route::put('/{product}', 'update')->name('update');
            Route::delete('/{product}', 'destroy')->name('destroy');
        });

    // Settings routes
    Route::prefix('admin/settings')
        ->controller(SettingController::class)
        ->group(function () {
            Route::get('/', 'index')->name('admin.settings');

            Route::post('/color', 'storeColor')->name('admin.settings.color.store');
            Route::delete('/color/{color}', 'destroyColor')->name('admin.settings.color.destroy');

            Route::post('/size', 'storeSize')->name('admin.settings.size.store');
            Route::delete('/size/{size}', 'destroySize')->name('admin.settings.size.destroy');
        });
});
