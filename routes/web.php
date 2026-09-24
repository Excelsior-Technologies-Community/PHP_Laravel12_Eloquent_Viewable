<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ViewAnalyticsController;


/*
|--------------------------------------------------------------------------
| Product Routes
|--------------------------------------------------------------------------
*/

Route::get('/products', [ProductController::class, 'index'])
    ->name('products.index');

Route::get('/products/create', [ProductController::class, 'create'])
    ->name('products.create');

Route::post('/products/store', [ProductController::class, 'store'])
    ->name('products.store');

Route::get('/products/show/{id}', [ProductController::class, 'show'])
    ->name('products.show');

Route::get('/products/edit/{id}', [ProductController::class, 'edit'])
    ->name('products.edit');

Route::put('/products/update/{id}', [ProductController::class, 'update'])
    ->name('products.update');

Route::delete('/products/delete/{id}', [ProductController::class, 'destroy'])
    ->name('products.destroy');


/*
|--------------------------------------------------------------------------
| View Analytics Routes
|--------------------------------------------------------------------------
*/

// Dashboard
Route::get(
    '/view-analytics',
    [ViewAnalyticsController::class, 'dashboard']
)->name('analytics.dashboard');


// Product Search / Filter / Pagination
Route::get(
    '/view-analytics/products',
    [ViewAnalyticsController::class, 'products']
)->name('analytics.products');


// View History
Route::get(
    '/view-analytics/history',
    [ViewAnalyticsController::class, 'history']
)->name('analytics.history');


// CSV Export
Route::get(
    '/view-analytics/history/export',
    [ViewAnalyticsController::class, 'exportCsv']
)->name('analytics.history.export');


// Delete individual view
Route::delete(
    '/view-analytics/history/{id}',
    [ViewAnalyticsController::class, 'deleteView']
)->name('analytics.history.delete');