<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SpiceOrderController;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public order portal.
// These are unauthenticated and world-reachable, so submissions are throttled
// (stops the pending-orders queue being flooded by a script) and so are the
// lookup endpoints (slows brute-forcing a reference or shop id).
Route::get('/order',                         [OrderController::class, 'form'])->name('order.form');
Route::post('/order',                        [OrderController::class, 'store'])
    ->middleware('throttle:10,1')->name('order.store');
Route::get('/order/confirm/{reference}',     [OrderController::class, 'confirm'])
    ->middleware('throttle:30,1')->name('order.confirm');
Route::get('/order/shop/{shop}/info',        [OrderController::class, 'shopInfo'])
    ->middleware('throttle:60,1')->name('order.shop.info');

// Public stock availability
Route::get('/stock',                         [OrderController::class, 'stockView'])->name('stock.public');
Route::get('/stock/data',                    [OrderController::class, 'stockData'])->name('stock.data');

// Public spice order portal (Chilli, Turmeric, ...) — separate from salt
Route::get('/spice-order',                     [SpiceOrderController::class, 'form'])->name('spice-order.form');
Route::post('/spice-order',                    [SpiceOrderController::class, 'store'])
    ->middleware('throttle:10,1')->name('spice-order.store');
Route::get('/spice-order/confirm/{reference}', [SpiceOrderController::class, 'confirm'])
    ->middleware('throttle:30,1')->name('spice-order.confirm');
Route::get('/spice-order/shop/{shop}/info',    [SpiceOrderController::class, 'shopInfo'])
    ->middleware('throttle:60,1')->name('spice-order.shop.info');

// Public spice stock availability
Route::get('/spice-stock',                     [SpiceOrderController::class, 'stockView'])->name('spice-stock.public');
Route::get('/spice-stock/data',                [SpiceOrderController::class, 'stockData'])->name('spice-stock.data');




