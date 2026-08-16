<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\SpiceOrderController;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public order portal
Route::get('/order',                         [OrderController::class, 'form'])->name('order.form');
Route::post('/order',                        [OrderController::class, 'store'])->name('order.store');
Route::get('/order/confirm/{reference}',     [OrderController::class, 'confirm'])->name('order.confirm');
Route::get('/order/shop/{shop}/info',        [OrderController::class, 'shopInfo'])->name('order.shop.info');

// Public stock availability
Route::get('/stock',                         [OrderController::class, 'stockView'])->name('stock.public');
Route::get('/stock/data',                    [OrderController::class, 'stockData'])->name('stock.data');

// Public spice order portal (Chilli, Turmeric, ...) — separate from salt
Route::get('/spice-order',                     [SpiceOrderController::class, 'form'])->name('spice-order.form');
Route::post('/spice-order',                    [SpiceOrderController::class, 'store'])->name('spice-order.store');
Route::get('/spice-order/confirm/{reference}', [SpiceOrderController::class, 'confirm'])->name('spice-order.confirm');

// Public spice stock availability
Route::get('/spice-stock',                     [SpiceOrderController::class, 'stockView'])->name('spice-stock.public');
Route::get('/spice-stock/data',                [SpiceOrderController::class, 'stockData'])->name('spice-stock.data');




