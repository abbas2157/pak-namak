<?php


use App\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Route;


Route::get('/', function () {
    // return view('layout.master');
});
Route::get('home', [AdminController::class, 'home'])->name('home');
// Route::get('layout/login', [AdminController::class, 'login'])->name('admin.login');
// Route::post('layout/login', [AdminController::class, 'adminlogin'])->name('login');
// Route::get('posts/index', [AdminController::class, 'index'])->name('posts.index');
// Route::get('posts/create', [AdminController::class, 'create'])->name('posts.create');
// Route::post('posts/store', [AdminController::class, 'store'])->name('posts.store');
// Route::get('/posts/{id}/edit', [AdminController::class, 'edit'])->name('posts.edit');
// Route::post('posts/update/{id}', [AdminController::class, 'update'])->name('posts.update');
// Route::get('posts/delete/{id}', [AdminController::class, 'delete'])->name('posts.delete');




