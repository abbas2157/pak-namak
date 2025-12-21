<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AssetController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\Admin\TypeController;
use App\Http\Controllers\ProductionController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Admin\PackageController;

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
        Route::resource('employees', EmployeeController::class);
        Route::resource('assets', AssetController::class);
        Route::resource('salt-purchases',PurchaseController::class);
        Route::resource('productions', ProductionController::class);
    });
});
