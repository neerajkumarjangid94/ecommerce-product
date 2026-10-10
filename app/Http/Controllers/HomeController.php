<?php

namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    //product list for user.
    public function index()
    {
        $products = Product::with([
            'images',
            'variants.images',
            'variants.optionValues.optionValue.option'
        ])
            ->active()
            ->latest()
            ->paginate(12);

        return view('welcome', compact('products'));
    }

    //product details by id.
    public function show($id = null)
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
