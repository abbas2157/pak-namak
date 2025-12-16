<?php
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\Admin\VendorController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->name('admin.')->middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
        Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'auth'])->name('login');
    });
    Route::middleware('auth')->group(function () {
        Route::get('dashboard', [App\Http\Controllers\Admin\DashboardController::class, 'home'])->name('dashboard');

        Route::resource('vendors', VendorController::class);

        Route::resource('types', TypeController::class);
        Route::resource('package', PackageController::class);
    });
});