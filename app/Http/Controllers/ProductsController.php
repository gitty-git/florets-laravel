<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index()
    {
        // return Product::all();
        return Product::where('published', '1')->select('id', 'slug', 'name', 'main_image', 'title')->with('attributes')->orderBy('created_at', 'DESC')->get();
    }

    public function search($search) {
        return Product::where('name', 'LIKE', "%{$search}%")
        ->orWhere('slug', 'LIKE', "%{$search}%")->with('attributes')
        ->take(5)->get();
    }
}
