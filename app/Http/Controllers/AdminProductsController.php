<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
class AdminProductsController extends Controller
{
    public function index()
    {
        return Product::orderBy('created_at', 'DESC')->get(['name', 'main_image', 'published', 'price', 'size', 'id']);
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);
        $res = Product::create(
            [
                'main_image' => $request->main_image,
                'images' => $request->images,
                'name' => $request->name,
                'price' => $request->price,
                'size' => $request->size,
                'description' => $request->description,
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
                'images' => $request->images,
                'name' => $request->name,
                'price' => $request->price,
                'size' => $request->size,
                'description' => $request->description,
                'published' => $request->published
            ]
        );
        $res = Product::select('id', 'main_image', 'name', 'price', 'published', 'size')->where('id', $product->id)->first();
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
            "images"    => "required|json",
            'price' => 'required|integer',
            'size' => 'required|integer',
            'description' => 'nullable|string',
            'published' => 'required',
        ]);
    }
}
