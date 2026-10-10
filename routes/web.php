<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

require __DIR__ . '/auth.php';

Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/product/{id}', [App\Http\Controllers\HomeController::class, 'show'])->name('product.show');



Route::middleware('auth')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin_product');
    })->middleware(['auth', 'verified'])->name('dashboard');

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
    Route::post('/admin/product', [ProductController::class, 'store'])->name('admin.product.store');
    Route::get('/admin/product/{product}/edit', [ProductController::class, 'edit'])->name('admin.product.edit');
    Route::put('/admin/product/{product}', [ProductController::class, 'update'])->name('admin.product.update');
    Route::delete('/admin/product/{product}', [ProductController::class, 'destroy'])->name('admin.product.destroy');

    // Settings routes
    Route::prefix('admin/settings')->group(function () {
        Route::get('/',                 [SettingController::class, 'index'])->name('admin.settings');
        Route::post('/color',           [SettingController::class, 'storeColor'])->name('admin.settings.color.store');
        Route::delete('/color/{color}', [SettingController::class, 'destroyColor'])->name('admin.settings.color.destroy');
        Route::post('/size',            [SettingController::class, 'storeSize'])->name('admin.settings.size.store');
        Route::delete('/size/{size}',   [SettingController::class, 'destroySize'])->name('admin.settings.size.destroy');
    });
});
