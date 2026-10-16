<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/users');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');

    // It is only available before the first user exists.
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // The user CRUD requires an active session.
    Route::get('/users', [UserController::class, 'indexUsers'])->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
    Route::post('/users', [UserController::class, 'store'])->name('users.store');
    Route::get('/users/{userId}/edit', [UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{userId}', [UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{userId}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::get('/user/{userId}', [UserController::class, 'showUserById'])->name('user.show');
    Route::get('/user/name/{userName}', [UserController::class, 'showUserByName'])->name('user.showByName');
});
