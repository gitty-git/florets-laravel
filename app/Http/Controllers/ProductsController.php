<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index()
    {
        // return Product::all();
        return Product::with('sizes:slug,product_id,id')->where('published', '1')
        ->select('id', 'slug', 'name', 'main_image', 'title')
        ->orderBy('created_at', 'ASC')->get();
    }

    public function search($search) {
        return Product::where('name', 'LIKE', "%{$search}%")
        ->orWhere('slug', 'LIKE', "%{$search}%")->with('sizes')
        ->take(5)->get();
    }
}
