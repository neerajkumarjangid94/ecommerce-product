<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use App\Models\Size;
use App\Models\Product;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    //After login by admin product page.
    public function admin_product()
    {
        $products = Product::with(['images', 'options', 'variants.images', 'variants.optionValues.optionValue'])->latest()->get();
        return view('admin_product', compact('products'));
    }

    //After login by admin new product page.
    public function admin_newproduct()
    {
        $colors = Color::latest()->get();
        $sizes  = Size::latest()->get();

        return view('admin.admin_newproduct', compact('colors', 'sizes'));
    }
}
