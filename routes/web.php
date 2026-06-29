<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OrderController;

// Landing page
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Public order portal
Route::get('/order',                         [OrderController::class, 'form'])->name('order.form');
Route::post('/order',                        [OrderController::class, 'store'])->name('order.store');
Route::get('/order/confirm/{reference}',     [OrderController::class, 'confirm'])->name('order.confirm');
Route::get('/order/shop/{shop}/info',        [OrderController::class, 'shopInfo'])->name('order.shop.info');




