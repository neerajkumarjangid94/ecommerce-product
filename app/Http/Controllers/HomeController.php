<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::with(['images', 'variants.images', 'variants.optionValues.optionValue.option'])
            ->active()
            ->latest()
            ->paginate(12);

        return view('welcome', compact('products'));
    }

    public function show($id)
    {
        $product = Product::with([
            'images', 
            'variants.images', 
            'variants.optionValues.optionValue.option'
        ])
        ->active()
        ->findOrFail($id);

        return view('product_detail', compact('product'));
    }
}
