<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController as AuthController;
use App\Http\Controllers\UserController as UserController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.attempt');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/user/update/{userId}', [UserController::class, 'updateUser'])->name('user.update');
Route::get('/user/{userId}', [UserController::class, 'showUserById'])->name('user.show');
Route::get('/users', [UserController::class, 'indexUsers'])->name('users.index');
Route::get('/user/name/{userName}', [UserController::class, 'showUserByName'])->name('user.showByName');

