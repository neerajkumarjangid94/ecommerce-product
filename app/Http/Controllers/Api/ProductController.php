<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\StoreProductRequest;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends BaseApiController
{
    public function __construct(protected ProductService $productService) {}

    /**
     * New product add by admin.
     */
    public function ProductCreate(StoreProductRequest $request)
    {
        $product = $this->productService->store($request->validated());

        // Load relations for response
        $product->load([
            'images',
            'options.values',
            'variants.images',
            'variants.optionValues.optionValue.option',
        ]);

        return $this->sendResponse($product, 'Product created successfully', 201);
    }

    /**
     * Display a listing of all products for admin (active and inactive).
     */
    public function adminProductListing(Request $request)
    {
        $limit = (int) $request->input('limit', 12);

        $products = Product::with([
            'images',
            'options.values',
            'variants.images',
            'variants.optionValues.optionValue.option',
        ])
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($limit);

        return $this->sendResponse($products, 'Admin products retrieved successfully');
    }


    /**
     * Display a listing of products with eager-loaded relations.
     */
    public function productListing(Request $request)
    {
        $limit = (int) $request->input('limit', 12);

        $products = Product::with([
            'images',
            'options.values',
            'variants.images',
            'variants.optionValues.optionValue.option',
        ])
            ->active()
            ->when($request->filled('search'), function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%');
            })
            ->latest()
            ->paginate($limit);

        return $this->sendResponse($products, 'Products retrieved successfully');
    }

    /**
     * Display the specified product with all its relations.
     */
    public function productDetails($id): JsonResponse
    {
        $product = Product::with([
            'images',
            'options.values',
            'variants.images',
            'variants.optionValues.optionValue.option',
        ])->active()->find($id);

        if (!$product) {
            return $this->sendError('Product not found', [], 404);
        }

        return $this->sendResponse($product, 'Product details retrieved successfully');
    }



    /**
     * Update the specified product in storage reusing StoreProductRequest & ProductService.
     */
    public function update(StoreProductRequest $request, $id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Product not found', [], 404);
        }

        $updatedProduct = $this->productService->update($product, $request->validated());

        $updatedProduct->load([
            'images',
            'options.values',
            'variants.images',
            'variants.optionValues.optionValue.option',
        ]);

        return $this->sendResponse($updatedProduct, 'Product updated successfully');
    }

    /**
     * Remove the specified product from storage (soft delete).
     */
    public function destroy($id): JsonResponse
    {
        $product = Product::find($id);

        if (!$product) {
            return $this->sendError('Product not found', [], 404);
        }

        $product->delete();

        return $this->sendResponse(null, 'Product deleted successfully');
    }
}
