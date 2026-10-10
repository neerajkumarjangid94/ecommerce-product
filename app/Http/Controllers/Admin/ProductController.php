<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;

class ProductController extends Controller
{
    public function __construct(protected ProductService $productService)
    {
    }

    /**
     * Store a newly created product in the database.
     */
    public function store(StoreProductRequest $request): RedirectResponse
    {
        $this->productService->store($request->validated());

        return redirect()
            ->route('admin.product')
            ->with('success', 'Product created successfully!');
    }
}
