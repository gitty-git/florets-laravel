<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Support\Str;

class AdminProductsController extends Controller
{
    public function index()
    {
        return Product::with('attributes')->orderBy('created_at', 'DESC')->get();
    }

    public function show(Product $product) 
    {
        return $product;
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);
        $res = Product::create(
            [
                'main_image' => $request->main_image,
                'name' => $request->name,
                'slug' => Str::slug($request->name),
                'title' => $request->title,
                'description' => $request->description,
                'composition' => $request->composition,
                'published' => $request->published
            ]
        );
        return $res;
    }

    public function update(Request $request, Product $product)
    {
        $this->validateRequest($request);
        Product::where('id', $product->id)->update(
            [ 
                'main_image' => $request->main_image,
                'slug' => Str::slug($request->name),
                'name' => $request->name,
                'title' => $request->title,
                'description' => $request->description,
                'composition' => $request->description,
                'published' => $request->published
            ]
        );
        // $res = Product::select('id', 'main_image', 'name', 'price', 'published', 'size')->where('id', $product->id)->first();
        $res = Product::select('id', 'main_image', 'name', 'published')->with('attributes')->where('id', $product->id)->first();
        return $res;
    }

    public function destroy(Request $request, Product $product)
    {
        Product::find($product->id)->delete();
    }

    private function validateRequest($request)
    {
        return $request->validate([
            'main_image' => 'required|string',
            'name' => 'required|string',
            'title' => 'nullable|string',
            'description' => 'nullable|string',
            'composition' => 'nullable|string',
            'published' => 'required',
        ]);
    }
}
