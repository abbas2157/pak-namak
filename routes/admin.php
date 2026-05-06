<?php
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::middleware('guest')->group(function () {
        Route::get('login', [App\Http\Controllers\Admin\AuthController::class, 'login'])->name('login');
        Route::post('login', [App\Http\Controllers\Admin\AuthController::class, 'auth'])->name('login.submit');
    });

    Route::middleware('auth')->group(function () {
        Route::get('logout', [App\Http\Controllers\Admin\AuthController::class, 'logout'])->name('logout');
        Route::get('/', [App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');

        Route::resource('purchases', App\Http\Controllers\Admin\PurchaseController::class, ['as' => 'admin']);
        Route::resource('productions', App\Http\Controllers\Admin\ProductionController::class, ['as' => 'admin']);
        Route::resource('vendors', App\Http\Controllers\Admin\VendorController::class, ['as' => 'admin']);
        Route::resource('sales', App\Http\Controllers\Admin\SaleController::class, ['as' => 'admin']);
        Route::resource('shops', App\Http\Controllers\Admin\ShopController::class, ['as' => 'admin']);

        // Shop sales (new page for shop-wise sales list)
        Route::get('sales-by-shop', [App\Http\Controllers\Admin\ShopSalesController::class, 'index'])->name('admin.sales.by_shop');

        // Sales report (all sales + Dalla/Thailas/Packages totals + print/PDF via browser)
        Route::get('sales-report', [App\Http\Controllers\Admin\SalesReportController::class, 'index'])->name('admin.sales.report');
        Route::get('sales-report/pdf', [App\Http\Controllers\Admin\SalesReportController::class, 'pdfAll'])->name('admin.sales.report.pdf');


        Route::resource('types', App\Http\Controllers\Admin\TypeController::class, ['as' => 'admin']);
        Route::resource('employees', App\Http\Controllers\Admin\EmployeeController::class, ['as' => 'admin']);
        Route::resource('expenses', App\Http\Controllers\Admin\ExpenseController::class, ['as' => 'admin']);
        Route::resource('assets', App\Http\Controllers\Admin\AssetController::class);

        // Receipt (new tab / print-friendly)
        Route::get('sales/{id}/receipt', \App\Http\Controllers\Admin\SaleReceiptController::class)
            ->name('admin.sales.receipt');

    });
});
