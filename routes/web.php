<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostsController;
use Illuminate\Support\Facades\Route;



Route::get('/posts', [PostsController::class, 'index'])->name('posts.index');
Route::get('/posts/{id}', [PostsController::class, 'show'])->name('posts.show');


Route::post('/login',[AuthController::class, 'login'])->name('login');
Route::post('/register',[AuthController::class, 'register'])->name('register');

Route::middleware('auth')->group(function () {
    Route::get('/create', [PostsController::class, 'create'])->name('posts.create');
    Route::post('/posts', [PostsController::class, 'store'])->name('posts.store');
    Route::put('/posts/{id}', [PostsController::class, 'update'])->name('posts.update');
    Route::delete('/posts/{id}', [PostsController::class, 'destroy'])->name('posts.destroy');
    Route::post('/logout',[AuthController::class, 'logout'])->name('logout');
});
