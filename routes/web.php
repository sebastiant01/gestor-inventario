<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/products');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1')->name('login.attempt');

    // El registro público solamente crea la primera cuenta administrativa.
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:3,1')->name('register.attempt');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Gestión de usuarios: cada operación exige su permiso correspondiente.
    Route::get('/users', [UserController::class, 'indexUsers'])
        ->middleware('permission:users.show')->name('users.index');
    Route::get('/users/create', [UserController::class, 'create'])
        ->middleware('permission:users.create')->name('users.create');
    Route::post('/users', [UserController::class, 'store'])
        ->middleware('permission:users.create')->name('users.store');
    Route::get('/users/{userId}/edit', [UserController::class, 'edit'])
        ->middleware('permission:users.update')->name('users.edit');
    Route::put('/users/{userId}', [UserController::class, 'update'])
        ->middleware('permission:users.update')->name('users.update');
    Route::delete('/users/{userId}', [UserController::class, 'destroy'])
        ->middleware('permission:users.delete')->name('users.destroy');
    Route::get('/user/{userId}', [UserController::class, 'showUserById'])
        ->middleware('permission:users.update')->name('user.show');
    Route::get('/user/name/{userName}', [UserController::class, 'showUserByName'])
        ->middleware('permission:users.update')->name('user.showByName');

    // Categorías.
    Route::get('/categories', [CategoryController::class, 'indexCategories'])
        ->middleware('permission:categories.show')->name('categories.index');
    Route::get('/categories/create', [CategoryController::class, 'createCategory'])
        ->middleware('permission:categories.create')->name('categories.create');
    Route::post('/categories', [CategoryController::class, 'store'])
        ->middleware('permission:categories.create')->name('categories.store');
    Route::get('/categories/{idCategory}/edit', [CategoryController::class, 'edit'])
        ->middleware('permission:categories.update')->name('categories.edit');
    Route::put('/categories', [CategoryController::class, 'update'])
        ->middleware('permission:categories.update')->name('categories.update');
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy'])
        ->middleware('permission:categories.delete')->name('categories.destroy');

    // Proveedores.
    Route::get('/suppliers', [SupplierController::class, 'index'])
        ->middleware('permission:suppliers.show')->name('suppliers.index');
    Route::get('/suppliers/search', [SupplierController::class, 'searchSupplierByName'])
        ->middleware('permission:suppliers.show')->name('suppliers.search');
    Route::get('/suppliers/create', [SupplierController::class, 'create'])
        ->middleware('permission:suppliers.create')->name('suppliers.create');
    Route::post('/suppliers', [SupplierController::class, 'store'])
        ->middleware('permission:suppliers.create')->name('suppliers.store');
    Route::get('/suppliers/{id}/edit', [SupplierController::class, 'edit'])
        ->middleware('permission:suppliers.update')->name('suppliers.edit');
    Route::put('/suppliers', [SupplierController::class, 'update'])
        ->middleware('permission:suppliers.update')->name('suppliers.update');
    Route::delete('/suppliers/{id}', [SupplierController::class, 'delete'])
        ->middleware('permission:suppliers.delete')->name('suppliers.destroy');

    // Productos.
    Route::get('/products', [ProductController::class, 'index'])
        ->middleware('permission:products.show')->name('products.index');
    Route::get('/products/category/{categoryId}', [ProductController::class, 'getByCategory'])
        ->middleware('permission:products.show')->name('products.by-category');
    Route::get('/products/create', [ProductController::class, 'create'])
        ->middleware('permission:products.create')->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])
        ->middleware('permission:products.create')->name('products.store');
    Route::get('/products/{id}/edit', [ProductController::class, 'edit'])
        ->middleware('permission:products.update')->name('products.edit');
    Route::put('/products', [ProductController::class, 'update'])
        ->middleware('permission:products.update')->name('products.update');
    Route::delete('/products/{id}', [ProductController::class, 'delete'])
        ->middleware('permission:products.delete')->name('products.destroy');

   // Inventarios y operaciones sobre sus productos.
Route::get('/inventories', [InventoryController::class, 'index'])
    ->middleware('permission:inventories.show')->name('inventories.index');

Route::get('/inventories/create', [InventoryController::class, 'create'])
    ->middleware('permission:inventories.create')->name('inventories.create');

Route::post('/inventories', [InventoryController::class, 'store'])
    ->middleware('permission:inventories.create')->name('inventories.store');

// Primero las rutas específicas de productos.
Route::post('/inventories/products', [InventoryController::class, 'addProduct'])
    ->middleware('permission:inventories.products.manage')->name('inventories.products.add');

Route::delete('/inventories/products', [InventoryController::class, 'removeProduct'])
    ->middleware('permission:inventories.products.manage')->name('inventories.products.remove');

Route::put('/inventories/products', [InventoryController::class, 'updateProductQuantity'])
    ->middleware('permission:inventories.products.manage')->name('inventories.products.quantity');

// Después las rutas que utilizan identificadores.
Route::get('/inventories/{id}/edit', [InventoryController::class, 'edit'])
    ->middleware('permission:inventories.update')->name('inventories.edit');

Route::put('/inventories', [InventoryController::class, 'update'])
    ->middleware('permission:inventories.update')->name('inventories.update');

Route::delete('/inventories/{id}', [InventoryController::class, 'delete'])
    ->middleware('permission:inventories.delete')->name('inventories.destroy');
});
