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

        Route::get('cash-ledger', [App\Http\Controllers\Admin\CashLedgerController::class, 'index'])->name('admin.cash_ledger.index');
        Route::post('cash-ledger/opening-balance', [App\Http\Controllers\Admin\CashLedgerController::class, 'setOpeningBalance'])->name('admin.cash_ledger.opening_balance');
        Route::post('cash-ledger/manual', [App\Http\Controllers\Admin\CashLedgerController::class, 'storeManual'])->name('admin.cash_ledger.manual.store');
        Route::delete('cash-ledger/{ledger}', [App\Http\Controllers\Admin\CashLedgerController::class, 'destroyManual'])->name('admin.cash_ledger.destroy');

        Route::post('accounts', [App\Http\Controllers\Admin\AccountController::class, 'store'])->name('admin.accounts.store');
        Route::put('accounts/{account}', [App\Http\Controllers\Admin\AccountController::class, 'update'])->name('admin.accounts.update');
        Route::delete('accounts/{account}', [App\Http\Controllers\Admin\AccountController::class, 'destroy'])->name('admin.accounts.destroy');
        Route::post('accounts/{account}/reactivate', [App\Http\Controllers\Admin\AccountController::class, 'reactivate'])->name('admin.accounts.reactivate');

        Route::get('stocks', [App\Http\Controllers\Admin\StockController::class, 'index'])->name('admin.stocks.index');
        Route::post('stocks/addition', [App\Http\Controllers\Admin\StockController::class, 'storeAddition'])->name('admin.stocks.addition');
        Route::post('stocks/adjustment', [App\Http\Controllers\Admin\StockController::class, 'storeAdjustment'])->name('admin.stocks.adjustment');

        Route::resource('purchases', App\Http\Controllers\Admin\PurchaseController::class, ['as' => 'admin']);
        Route::post('purchases/{purchase}/payments', [App\Http\Controllers\Admin\PurchaseController::class, 'recordPayment'])->name('admin.purchases.payments.store');
        Route::get('purchases/{purchase}/payments', [App\Http\Controllers\Admin\PurchaseController::class, 'payments'])->name('admin.purchases.payments.index');
        Route::delete('purchases/{purchase}/payments/{payment}', [App\Http\Controllers\Admin\PurchaseController::class, 'destroyPayment'])->name('admin.purchases.payments.destroy');
        Route::resource('productions', App\Http\Controllers\Admin\ProductionController::class, ['as' => 'admin']);
        Route::resource('vendors', App\Http\Controllers\Admin\VendorController::class, ['as' => 'admin']);
        Route::post('vendors/{vendor}/payments', [App\Http\Controllers\Admin\VendorController::class, 'recordPayment'])->name('admin.vendors.payments.store');
        Route::get('vendor-payments', [App\Http\Controllers\Admin\VendorController::class, 'paymentForm'])->name('admin.vendors.payment_form');
        Route::resource('sales', App\Http\Controllers\Admin\SaleController::class, ['as' => 'admin']);
        Route::post('sales/{sale}/quick-update', [App\Http\Controllers\Admin\SaleController::class, 'quickUpdate'])->name('admin.sales.quick_update');
        Route::post('sales/{sale}/payments', [App\Http\Controllers\Admin\SaleController::class, 'addPayment'])->name('admin.sales.payments.store');
        Route::delete('sales/{sale}/payments/{payment}', [App\Http\Controllers\Admin\SaleController::class, 'destroyPayment'])->name('admin.sales.payments.destroy');
        Route::resource('shops', App\Http\Controllers\Admin\ShopController::class, ['as' => 'admin']);
        Route::get('shops/{shop}/info', [App\Http\Controllers\Admin\ShopController::class, 'info'])->name('admin.shops.info');
        Route::post('shops/{shop}/payments', [App\Http\Controllers\Admin\ShopController::class, 'recordPayment'])->name('admin.shops.payments.store');
        Route::get('shop-payments', [App\Http\Controllers\Admin\ShopController::class, 'paymentForm'])->name('admin.shops.payment_form');
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
        Route::post('employees/{employee}/advances', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'storeAdvance'])->name('admin.employees.advances.store');
        Route::post('employees/{employee}/salaries', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'storeSalary'])->name('admin.employees.salaries.store');
        Route::get('employees/{employee}/salary-preview', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'preview'])->name('admin.employees.salaries.preview');
        Route::delete('employees/{employee}/salaries/{salary}', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'destroy'])->name('admin.employees.salaries.destroy');
        Route::get('employees/{employee}/salaries/{salary}/payslip', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'payslip'])->name('admin.employees.salaries.payslip');
        Route::post('employees/{employee}/absences', [App\Http\Controllers\Admin\EmployeeAbsenceController::class, 'store'])->name('admin.employees.absences.store');
        Route::delete('employees/{employee}/absences/{absence}', [App\Http\Controllers\Admin\EmployeeAbsenceController::class, 'destroy'])->name('admin.employees.absences.destroy');
        Route::get('employee-advances', [App\Http\Controllers\Admin\EmployeeSalaryController::class, 'advanceForm'])->name('admin.employees.advance_form');

        Route::get('holidays', [App\Http\Controllers\Admin\CompanyHolidayController::class, 'index'])->name('admin.holidays.index');
        Route::post('holidays', [App\Http\Controllers\Admin\CompanyHolidayController::class, 'store'])->name('admin.holidays.store');
        Route::delete('holidays/{holiday}', [App\Http\Controllers\Admin\CompanyHolidayController::class, 'destroy'])->name('admin.holidays.destroy');
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

        // ═══════════════════════════════════════════════════════════
        // SPICES MODULE — separate from salt (Chilli, Turmeric, ...)
        // ═══════════════════════════════════════════════════════════

        // ->parameters() pins each resource's route wildcard to match the
        // controller's chosen variable name — Laravel's default wildcard for a
        // multi-word kebab resource (e.g. "spice-sales") is the snake_case
        // "spice_sale", which won't implicitly bind to a camelCase $spiceSale
        // parameter and would silently inject an empty, unsaved model instead.
        Route::resource('spice-types', App\Http\Controllers\Admin\SpiceTypeController::class, ['as' => 'admin'])
            ->parameters(['spice-types' => 'type']);

        // URI is plural ("spice-stocks") to avoid colliding with the public
        // singular "/spice-stock" viewing page below — same admin-plural /
        // public-singular convention salt uses ("stocks" vs "stock"). Route
        // *names* stay "admin.spice-stock.*" (unaffected by the URI change).
        Route::get('spice-stocks', [App\Http\Controllers\Admin\SpiceStockController::class, 'index'])->name('admin.spice-stock.index');
        Route::post('spice-stocks/addition', [App\Http\Controllers\Admin\SpiceStockController::class, 'storeAddition'])->name('admin.spice-stock.addition');
        Route::post('spice-stocks/adjustment', [App\Http\Controllers\Admin\SpiceStockController::class, 'storeAdjustment'])->name('admin.spice-stock.adjustment');

        Route::resource('spice-purchases', App\Http\Controllers\Admin\SpicePurchaseController::class, ['as' => 'admin']);
        Route::post('spice-purchases/{purchase}/payments', [App\Http\Controllers\Admin\SpicePurchaseController::class, 'recordPayment'])->name('admin.spice-purchases.payments.store');
        Route::get('spice-purchases/{purchase}/payments', [App\Http\Controllers\Admin\SpicePurchaseController::class, 'payments'])->name('admin.spice-purchases.payments.index');
        Route::delete('spice-purchases/{purchase}/payments/{payment}', [App\Http\Controllers\Admin\SpicePurchaseController::class, 'destroyPayment'])->name('admin.spice-purchases.payments.destroy');

        Route::resource('spice-sales', App\Http\Controllers\Admin\SpiceSaleController::class, ['as' => 'admin'])
            ->parameters(['spice-sales' => 'spiceSale']);
        Route::post('spice-sales/{spiceSale}/quick-update', [App\Http\Controllers\Admin\SpiceSaleController::class, 'quickUpdate'])->name('admin.spice-sales.quick_update');
        Route::post('spice-sales/{spiceSale}/payments', [App\Http\Controllers\Admin\SpiceSaleController::class, 'addPayment'])->name('admin.spice-sales.payments.store');
        Route::delete('spice-sales/{spiceSale}/payments/{payment}', [App\Http\Controllers\Admin\SpiceSaleController::class, 'destroyPayment'])->name('admin.spice-sales.payments.destroy');

        Route::get('spice-orders',                    [App\Http\Controllers\Admin\SpiceOrderAdminController::class, 'index'])->name('admin.spice-orders.index');
        Route::get('spice-orders/{spiceOrder}',        [App\Http\Controllers\Admin\SpiceOrderAdminController::class, 'show'])->name('admin.spice-orders.show');
        Route::post('spice-orders/{spiceOrder}/confirm', [App\Http\Controllers\Admin\SpiceOrderAdminController::class, 'confirm'])->name('admin.spice-orders.confirm');
        Route::post('spice-orders/{spiceOrder}/reject', [App\Http\Controllers\Admin\SpiceOrderAdminController::class, 'reject'])->name('admin.spice-orders.reject');
        Route::get('spice-orders/{spiceOrder}/to-sale', [App\Http\Controllers\Admin\SpiceOrderAdminController::class, 'toSale'])->name('admin.spice-orders.to_sale');

    });
});
