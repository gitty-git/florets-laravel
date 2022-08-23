<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return Product::all();
    }

    public function search($search) {
        return Product::where('name', 'LIKE', "%{$search}%")
        ->orWhere('id', 'LIKE', "%{$search}%")
        ->take(5)->get();
    }
}
