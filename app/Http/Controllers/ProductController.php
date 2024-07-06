<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'product_price' => 'required',
            'product_description' => 'required|string',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $images = [];
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $name = time().rand(1,100).'.'.$file->extension();
                $file->move(public_path('images'), $name); 
                // $path = $file->store('images');
                $images[] = $name;
            }
        }

        $product = new Product();
        $product->product_name = $request->product_name;
        $product->product_price = $request->product_price;
        $product->product_description = $request->product_description;
        $product->product_images = $images;
        $product->save();

        return response()->json(['success' => 'Product added successfully!']);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'product_name' => 'required|string',
            'product_price' => 'required',
            'product_description' => 'required|string',
            'product_images.*' => 'image|mimes:jpeg,png,jpg,gif,svg',
        ]);

        $product = Product::findOrFail($id);

        $images = $product->product_images;
        if ($request->hasFile('product_images')) {
            foreach ($request->file('product_images') as $file) {
                $path = $file->store('public/images');
                $images[] = basename($path);
            }
        }

        $product->update([
            'product_name' => $request->product_name,
            'product_price' => $request->product_price,
            'product_description' => $request->product_description,
            'product_images' => $images,
        ]);

        return response()->json(['success' => 'Product updated successfully!']);
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        return response()->json($product);
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        $product->delete();
        return response()->json(['success' => 'Product deleted successfully!']);
    }

    public function product_list()
    {
        $products = Product::all();
        return response()->json($products);
    }
}
