<?php

namespace App\Http\Controllers;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::withCount('reviews')
            ->withAvg('reviews', 'rating')
            ->latest()
            ->get();
        return view('user.products', compact('products'));
    }
}