<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    public function index()
    {
        return Product::where('published', '1')->orderBy('created_at', 'DESC')->get();
    }

    public function search($search) {
        return Product::where('name', 'LIKE', "%{$search}%")
        ->orWhere('id', 'LIKE', "%{$search}%")
        ->take(5)->get();
    }
}
