#  PHP_Laravel12_Eloquent_Viewable

<p align="center">
<a href="#"><img src="https://img.shields.io/badge/Laravel-12-red?logo=laravel" alt="Laravel Version"></a>
<a href="#"><img src="https://img.shields.io/badge/PHP-8.x-blue?logo=php" alt="PHP Version"></a>
<a href="#"><img src="https://img.shields.io/badge/MySQL-Database-orange?logo=mysql" alt="Database"></a>
<a href="#"><img src="https://img.shields.io/badge/Eloquent-ORM-success" alt="Eloquent ORM"></a>
<a href="#"><img src="https://img.shields.io/badge/Eloquent--Viewable-View%20Counter-green" alt="View Counter"></a>
<a href="#"><img src="https://img.shields.io/badge/Blade-Template-yellow?logo=laravel" alt="Blade"></a>
</p>

A **Laravel 12 CRUD application** that manages products and automatically counts how many times each product page is viewed using the **Eloquent Viewable** package.

---

##  Overview

This project demonstrates how to implement:

* Manual CRUD operations in Laravel (without resource routes)
* Product page view tracking
* Database-based analytics using Eloquent
* Clean UI using Blade templates and custom CSS

Each time a user opens a product detail page, the system records and updates the view count automatically.

---

##  Features

* Product CRUD (Create, Read, Update, Delete)
* Product View Counter
* Manual Routing (No Resource Controller)
* Blade + CSS Styled UI
* Database View Tracking
* Eloquent ORM Integration
* Clean MVC Structure

---

##  Folder Structure

```
app/
 ├── Models/
 │    └── Product.php
 ├── Http/
 │    └── Controllers/
 │         └── ProductController.php

resources/
 └── views/
      └── products/
           ├── index.blade.php
           ├── create.blade.php
           ├── edit.blade.php
           └── show.blade.php

routes/
 └── web.php

database/
 └── migrations/
```

---

## 1. Technologies Used

* Laravel 12
* PHP
* MySQL
* Blade Template Engine
* HTML & CSS
* Eloquent ORM
* Eloquent-Viewable Package

---

## 2. Project Installation

### Step 1 — Create Laravel Project

```bash
composer create-project laravel/laravel viewable-demo
```

### Step 2 — Database Configuration

Open `.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=
```

---

## 3. Install View Counter Package

```bash
composer require cyrildewit/eloquent-viewable
```

Publish package:

```bash
php artisan vendor:publish --provider="CyrildeWit\EloquentViewable\EloquentViewableServiceProvider"
```

Run migration:

```bash
php artisan migrate
```

---

## 4. Create Product Module

Create Model + Migration:

```bash
php artisan make:model Product -m
```

---

## 5. Products Migration

**database/migrations/create_products_table.php**

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
```

Run:

```bash
php artisan migrate
```

---

## 6. Product Model

**app/Models/Product.php**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use CyrildeWit\EloquentViewable\Contracts\Viewable;
use CyrildeWit\EloquentViewable\InteractsWithViews;

class Product extends Model implements Viewable
{
    use InteractsWithViews;

    protected $fillable = [
        'name',
        'price'
    ];
}
```

---

## 7. Routes

**routes/web.php**

```php
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
```

---

## 8. Product Controller

Create Controller:

```bash
php artisan make:controller ProductController
```

**app/Http/Controllers/ProductController.php**

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductController extends Controller
{
    // LIST
    public function index()
    {
        $products = Product::latest()->get();
        return view('products.index', compact('products'));
    }

    // CREATE PAGE
    public function create()
    {
        return view('products.create');
    }

    // STORE
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        Product::create($request->all());

        return redirect()->route('products.index');
    }

    // SHOW + VIEW COUNT
    public function show($id)
    {
        $product = Product::findOrFail($id);

        // record view
        views($product)->record();

        return view('products.show', compact('product'));
    }

    // EDIT PAGE
    public function edit($id)
    {
        $product = Product::findOrFail($id);

        return view('products.edit', compact('product'));
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric'
        ]);

        $product = Product::findOrFail($id);
        $product->update($request->all());

        return redirect()->route('products.index');
    }

    // DELETE
    public function destroy($id)
    {
        Product::findOrFail($id)->delete();

        return redirect()->route('products.index');
    }
}
```

---

## 9. Blade Templates

Create folder:

```
resources/views/products/
```

---

### resources/views/products/index.blade.php

```html
<!DOCTYPE html>
<html>
<head>
<title>Products</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#eef2f7;
    margin:0;
    padding:40px;
}

/* CONTAINER */
.container{
    max-width:900px;
    margin:auto;
    background:white;
    padding:30px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    margin-bottom:25px;
}

/* ADD BUTTON */
.add-btn{
    background:#22c55e;
    color:white;
    padding:10px 16px;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

.add-btn:hover{
    background:#16a34a;
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
    margin-top:20px;
}

th{
    background:#111827;
    color:white;
    padding:12px;
}

td{
    padding:12px;
    text-align:center;
    border-bottom:1px solid #eee;
}

tr:hover{
    background:#f9fafb;
}

/* BUTTONS */
.btn{
    padding:6px 12px;
    border-radius:6px;
    color:white;
    text-decoration:none;
    font-size:14px;
    border:none;
    cursor:pointer;
}

.view{ background:#2563eb; }
.edit{ background:#f59e0b; }
.delete{ background:#dc2626; }

.view:hover{ background:#1d4ed8; }
.edit:hover{ background:#d97706; }
.delete:hover{ background:#b91c1c; }

.views{
    font-weight:bold;
    color:#16a34a;
}

</style>
</head>

<body>

<div class="container">

<h2>Product List</h2>

<a href="{{ route('products.create') }}" class="add-btn">+ Add Product</a>

<table>
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Price</th>
    <th>Views</th>
    <th>Actions</th>
</tr>

@foreach($products as $product)
<tr>
    <td>{{ $product->id }}</td>
    <td>{{ $product->name }}</td>
    <td>₹{{ $product->price }}</td>

    <td class="views">
        👁 {{ views($product)->count() }}
    </td>

    <td>
        <a class="btn view"
           href="{{ route('products.show',$product->id) }}">View</a>

        <a class="btn edit"
           href="{{ route('products.edit',$product->id) }}">Edit</a>

        <form action="{{ route('products.destroy',$product->id) }}"
              method="POST"
              style="display:inline;">
            @csrf
            @method('DELETE')
            <button class="btn delete">Delete</button>
        </form>
    </td>
</tr>
@endforeach

</table>

</div>

</body>
</html>
```

---

### resources/views/products/create.blade.php

```html
<!DOCTYPE html>
<html>
<head>
<title>Create Product</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#eef2f7;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
}

/* CARD */
.form-box{
    width:420px;
    background:#ffffff;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

h2{
    margin-bottom:25px;
    text-align:center;
}

/* LABEL */
label{
    font-weight:600;
    display:block;
    margin-bottom:6px;
}

/* INPUT */
input{
    width:100%;
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    margin-bottom:18px;
    font-size:15px;
    transition:.3s;
}

input:focus{
    outline:none;
    border-color:#22c55e;
    box-shadow:0 0 0 2px rgba(34,197,94,.2);
}

/* BUTTON */
button{
    width:100%;
    background:#22c55e;
    color:white;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#16a34a;
}

</style>
</head>

<body>

<div class="form-box">

<h2>Create Product</h2>

<form action="{{ route('products.store') }}" method="POST">
@csrf

<label>Name</label>
<input type="text" name="name" placeholder="Enter product name">

<label>Price</label>
<input type="number" name="price" placeholder="Enter price">

<button type="submit">Save Product</button>

</form>

</div>

</body>
</html>
```

---

### resources/views/products/edit.blade.php

```html
<!DOCTYPE html>
<html>
<head>
<title>Edit Product</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#eef2f7;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
}

.form-box{
    width:420px;
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
}

h2{
    text-align:center;
    margin-bottom:25px;
}

label{
    font-weight:600;
    display:block;
    margin-bottom:6px;
}

input{
    width:100%;
    padding:12px;
    border:1px solid #d1d5db;
    border-radius:8px;
    margin-bottom:18px;
    font-size:15px;
    transition:.3s;
}

input:focus{
    outline:none;
    border-color:#f59e0b;
    box-shadow:0 0 0 2px rgba(245,158,11,.2);
}

button{
    width:100%;
    background:#f59e0b;
    color:white;
    padding:12px;
    border:none;
    border-radius:8px;
    font-size:16px;
    font-weight:bold;
    cursor:pointer;
    transition:.3s;
}

button:hover{
    background:#d97706;
}

</style>
</head>

<body>

<div class="form-box">

<h2>Edit Product</h2>

<form action="{{ route('products.update',$product->id) }}" method="POST">
@csrf
@method('PUT')

<label>Name</label>
<input type="text" name="name" value="{{ $product->name }}">

<label>Price</label>
<input type="number" name="price" value="{{ $product->price }}">

<button type="submit">Update Product</button>

</form>

</div>

</body>
</html>
```

---

### resources/views/products/show.blade.php

```html
<!DOCTYPE html>
<html>
<head>
<title>Product Details</title>

<style>

body{
    font-family: Arial, sans-serif;
    background:#eef2f7;
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    margin:0;
}

/* CARD */
.card{
    width:420px;
    background:white;
    padding:35px;
    border-radius:12px;
    box-shadow:0 8px 25px rgba(0,0,0,0.08);
    text-align:center;
}

h2{
    margin-bottom:10px;
}

.price{
    font-size:20px;
    margin-bottom:15px;
}

.views{
    font-size:22px;
    font-weight:bold;
    color:#16a34a;
    margin:20px 0;
}

/* BUTTON */
.back{
    display:inline-block;
    margin-top:15px;
    padding:10px 18px;
    background:#2563eb;
    color:white;
    border-radius:8px;
    text-decoration:none;
    font-weight:bold;
}

.back:hover{
    background:#1d4ed8;
}

</style>
</head>

<body>

<div class="card">

<h2>{{ $product->name }}</h2>

<p class="price">Price: ₹{{ $product->price }}</p>

<div class="views">
    👁 {{ views($product)->count() }} Views
</div>

<a class="back" href="{{ route('products.index') }}">
    ← Back to Products
</a>

</div>

</body>
</html>
```

---

## 10. Final Output

* Product listing page

  <img width="963" height="372" alt="Screenshot 2026-02-24 155549" src="https://github.com/user-attachments/assets/e4907aef-709f-4407-845b-21aed058e502" />
---
* Create product page

  <img width="490" height="353" alt="Screenshot 2026-02-24 155321" src="https://github.com/user-attachments/assets/f6aa2403-d6f6-4bb5-a0bb-0960f21aa699" />
---
* Edit product page

  <img width="488" height="354" alt="Screenshot 2026-02-24 155403" src="https://github.com/user-attachments/assets/be0969b8-985d-48d5-80fc-ddebec48a27f" />
---
* Product detail page

  <img width="489" height="281" alt="Screenshot 2026-02-24 155434" src="https://github.com/user-attachments/assets/2ae11919-8e8a-40b8-8383-8c8b53576d8c" />
---
* Automatic view counting system

  <img width="963" height="372" alt="Screenshot 2026-02-24 155558" src="https://github.com/user-attachments/assets/2057a1b4-b4e6-4723-976e-1f85eddf54e8" />

---

