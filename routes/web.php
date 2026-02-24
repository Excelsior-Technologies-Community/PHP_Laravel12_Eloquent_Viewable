<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

/*
|--------------------------------------------------------------------------
| Product Routes (Manual)
|--------------------------------------------------------------------------
*/

// Product List
Route::get('/products', [ProductController::class,'index'])
        ->name('products.index');

// Create Form
Route::get('/products/create', [ProductController::class,'create'])
        ->name('products.create');

// Store Product
Route::post('/products/store', [ProductController::class,'store'])
        ->name('products.store');

// Show Product (View Counter)
Route::get('/products/show/{id}', [ProductController::class,'show'])
        ->name('products.show');

// Edit Form
Route::get('/products/edit/{id}', [ProductController::class,'edit'])
        ->name('products.edit');

// Update Product
Route::put('/products/update/{id}', [ProductController::class,'update'])
        ->name('products.update');

// Delete Product
Route::delete('/products/delete/{id}', [ProductController::class,'destroy'])
        ->name('products.destroy');