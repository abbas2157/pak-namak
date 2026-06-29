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
        Route::post('sales/{sale}/quick-update', [App\Http\Controllers\Admin\SaleController::class, 'quickUpdate'])->name('admin.sales.quick_update');
        Route::resource('shops', App\Http\Controllers\Admin\ShopController::class, ['as' => 'admin']);
        Route::get('shops/{shop}/info', [App\Http\Controllers\Admin\ShopController::class, 'info'])->name('admin.shops.info');
        Route::resource('cities', App\Http\Controllers\Admin\CityController::class, ['as' => 'admin'])->except(['create', 'show']);
        Route::get('cities/{city}/sales', [App\Http\Controllers\Admin\CityController::class, 'sales'])->name('admin.cities.sales');

        // Areas
        Route::get('areas', [App\Http\Controllers\Admin\AreaController::class, 'index'])->name('admin.areas.index');
        Route::get('cities/{city}/areas', [App\Http\Controllers\Admin\AreaController::class, 'byCity'])->name('admin.areas.by_city');
        Route::post('areas', [App\Http\Controllers\Admin\AreaController::class, 'store'])->name('admin.areas.store');
        Route::get('areas/{area}/edit', [App\Http\Controllers\Admin\AreaController::class, 'edit'])->name('admin.areas.edit');
        Route::put('areas/{area}', [App\Http\Controllers\Admin\AreaController::class, 'update'])->name('admin.areas.update');
        Route::delete('areas/{area}', [App\Http\Controllers\Admin\AreaController::class, 'destroy'])->name('admin.areas.destroy');

        // Shop sales (new page for shop-wise sales list)
        Route::get('sales-by-shop', [App\Http\Controllers\Admin\ShopSalesController::class, 'index'])->name('admin.sales.by_shop');

        // Sales report (all sales + Dalla/Thailas/Packages totals + print/PDF via browser)
        Route::get('sales-report', [App\Http\Controllers\Admin\SalesReportController::class, 'index'])->name('admin.sales.report');
        Route::get('sales-report/pdf', [App\Http\Controllers\Admin\SalesReportController::class, 'pdfAll'])->name('admin.sales.report.pdf');


        Route::resource('types', App\Http\Controllers\Admin\TypeController::class, ['as' => 'admin']);
        Route::resource('employees', App\Http\Controllers\Admin\EmployeeController::class, ['as' => 'admin']);
        Route::post('employees/{employee}/salaries', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'store'])->name('admin.employees.salaries.store');
        Route::delete('employees/{employee}/salaries/{salary}', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'destroy'])->name('admin.employees.salaries.destroy');
        Route::resource('expenses', App\Http\Controllers\Admin\ExpenseController::class, ['as' => 'admin']);
        Route::resource('assets', App\Http\Controllers\Admin\AssetController::class, ['as' => 'admin']);

        // Receipt (new tab / print-friendly)
        Route::get('sales/{id}/receipt', \App\Http\Controllers\Admin\SaleReceiptController::class)
            ->name('admin.sales.receipt');

        // Orders (from public order portal)
        Route::get('orders',                    [App\Http\Controllers\Admin\OrderAdminController::class, 'index'])->name('admin.orders.index');
        Route::get('orders/{order}',            [App\Http\Controllers\Admin\OrderAdminController::class, 'show'])->name('admin.orders.show');
        Route::post('orders/{order}/confirm',   [App\Http\Controllers\Admin\OrderAdminController::class, 'confirm'])->name('admin.orders.confirm');
        Route::post('orders/{order}/reject',    [App\Http\Controllers\Admin\OrderAdminController::class, 'reject'])->name('admin.orders.reject');
        Route::get('orders/{order}/to-sale',    [App\Http\Controllers\Admin\OrderAdminController::class, 'toSale'])->name('admin.orders.to_sale');

    });
});
