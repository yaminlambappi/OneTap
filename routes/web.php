<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\HomeController;

Route::get('/', [HomeController::class, 'index'])->name('home.index');


Route::get('/login-now', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/register-now', [AuthController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');

Route::get('/{user:username}', [HomeController::class, 'myProfile'])->name('my.profile');
Route::put('/profile/{user:username}/update', [HomeController::class, 'updateProfile'])
    ->name('profile.update')
    ->middleware('auth');Route::get('/{user:username}/edit', [HomeController::class, 'myProfile'])->name('profile.edit');
