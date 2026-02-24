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

        // 🔥 record view
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