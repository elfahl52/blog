<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dashboard\CategoryController;
use App\Http\Controllers\Dashboard\PostController as DashboardPostController;
use App\Http\Controllers\Dashboard\TagController;
use App\Http\Controllers\PostController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');
Route::post('/posts/{post:slug}/comments', [PostController::class, 'storeComment'])->name('posts.comments.store');
Route::delete('/posts/{post:slug}/comments/{comment}', [PostController::class, 'destroyComment'])->middleware('auth')->name('posts.comments.destroy');
Route::get('/category/{category:slug}', [PostController::class, 'byCategory'])->name('posts.category');
Route::get('/tag/{tag:slug}', [PostController::class, 'byTag'])->name('posts.tag');

Route::middleware('guest')->group(function () {
   Route::get('/register',[AuthController::class,'showRegister'])->name('register');
   Route::post('/register',[AuthController::class,'register']);
   Route::get('/login',[AuthController::class,'showLogin'])->name('login');
   Route::post('/login',[AuthController::class,'login'])->middleware('throttle:5.1');

});


Route::post('/logout',[AuthController::class,'logout'])->middleware('auth')->name('logout');

Route::middleware(['auth','role:admin,author'])->prefix('dashboard')->name('dashboard.')->group(function(){
  Route::resource('posts', DashboardPostController::class)->except('show');
});

Route::middleware(['auth', 'role:admin'])->prefix('dashboard')->name('dashboard.')->group(function () {
    Route::resource('categories', CategoryController::class)->except('show');
    Route::resource('tags', TagController::class)->except('show');
});
