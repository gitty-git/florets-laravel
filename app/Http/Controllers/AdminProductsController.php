<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use Illuminate\Http\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class AdminProductsController extends Controller
{
    public function index()
    {
        return Product::orderBy('created_at', 'DESC')->get(['name', 'main_image', 'published', 'price', 'size', 'id']);
    }

    public function store(Request $request)
    {
        $this->validateRequest($request);

        if ($request->main_image) {
            $image = $request->main_image;

            $path_to_main_img = Storage::disk('public')->put('images/products/' . Str::slug($request->name), new File($image), 'public');
        }

        $pathArr = [];

        if  ($request->images) {
            foreach ($request->images as $key => $img) {
                $path = Storage::disk('public')->put('images/products/' . Str::slug($request->name), new File($img), 'public');
                $pathArr[$key] = asset('storage/' . $path);
            }
        }

        $product = new Product();

        $product->name = $request->name;
        $product->main_image = asset('storage/' . $path_to_main_img);
        $product->images = json_encode($pathArr);
        $product->price = $request->price;
        $product->size = $request->size;
        $product->description = $request->description;
        $product->published = $request->published === 'true' ? true : false;

        $product->save();
    }

    public function update(Request $request, Product $product)
    {
        // return $request->main_image_path;
        // return $request->main_image;
        // if ($request->main_image) {
        //     return '1';
        // }
        // else {
        //     return '2';
        // };
        // return $request;

        // $this->validateRequest($request);

        // if ($request->main_image) {
        //     $image = $request->main_image;

        //     $path = Storage::disk('public')->put('images/products/' . Str::slug($request->name), new File($image), 'public');
        // }

        // $pathArr = [];

        // if ($request->images) {
        //     foreach ($request->images as $key => $img) {
        //         $path = Storage::disk('public')->put('images/products/' . Str::slug($request->name), new File($img), 'public');
        //         $pathArr[$key] = asset('storage/' . $path);
        //     }
        // }

        // $product = new Product();

        // $product->name = $request->name;
        // $product->main_image = asset('storage/' . $path);
        // $product->images = json_encode($pathArr);
        // $product->price = $request->price;
        // $product->size = $request->size;
        // $product->description = $request->description;
        // $product->published = $request->published === 'true' ? true : false;

        // return $request->main_image_path;
        // $main_image_path = null;
        // if  ($request->main_image_path) {
        //     $main_image_path['main_image'] = asset('storage/' . $request->main_image_path);
        // }
        // $request->published === 'true' ? true : false;
        $this->validateRequest($request);
        $res = Product::where('id', $product->id)->update(
            [
                'main_image' => $request->main_image,
                'name' => $request->name,
                'price' => $request->price,
                'size' => $request->size,
                'description' => $request->description,
                'published' => $request->published === 'true' ? true : false
            ]
        );
        return $res;
    }

    private function validateRequest($request)
    {
        return $request->validate([
            'main_image' => 'required|string',
            'name' => 'required|string',
            // "images"    => "required|array|size:3",
            // 'images.*' => 'required|mimes:jpg,jpeg,png,csv,txt,xlx,xls,pdf|max:2048',
            'price' => 'required|integer',
            'size' => 'required|integer',
            'description' => 'nullable|string',
            'published' => 'required',
        ]);
    }
}
