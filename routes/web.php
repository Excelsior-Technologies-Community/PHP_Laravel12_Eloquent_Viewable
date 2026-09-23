<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ViewAnalyticsController;

/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

// Product List
Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

// Create Form
Route::get('/products/create', [ProductController::class, 'create'])
    ->name('products.create');

// Store Product
Route::post('/products/store', [ProductController::class, 'store'])
    ->name('products.store');

// Show Product
Route::get('/products/show/{id}', [ProductController::class, 'show'])
    ->name('products.show');

// Edit Form
Route::get('/products/edit/{id}', [ProductController::class, 'edit'])
    ->name('products.edit');

// Update Product
Route::put('/products/update/{id}', [ProductController::class, 'update'])
    ->name('products.update');

// Delete Product
Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])
    ->name('products.destroy');


/*
|--------------------------------------------------------------------------
| View Analytics Routes
|--------------------------------------------------------------------------
*/

// Analytics Dashboard
Route::get('/view-analytics', [ViewAnalyticsController::class, 'dashboard'])
    ->name('analytics.dashboard');

// Product Search / Filtering / Pagination
Route::get('/view-analytics/products', [ViewAnalyticsController::class, 'products'])
    ->name('analytics.products');

// View History / Popularity Report
Route::get('/view-analytics/history', [ViewAnalyticsController::class, 'history'])
    ->name('analytics.history');

// CSV Export
Route::get('/view-analytics/history/export', [ViewAnalyticsController::class, 'exportCsv'])
    ->name('analytics.history.export');