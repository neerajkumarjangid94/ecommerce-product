<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductRequest;
use App\Services\ProductService;
use App\Models\Product;
use App\Models\Color;
use App\Models\Size;
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

    /**
     * Show the form for editing the specified product.
     */
    public function edit($id)
    {
        $product = Product::with(['images', 'variants.images', 'variants.optionValues.optionValue.option'])->findOrFail($id);
        
        $colors = Color::orderBy('name')->get();
        $sizes = Size::orderBy('name')->get();

        $existingVariantsMap = [];
        $selectedColors = [];
        $selectedSizes = [];

        foreach ($product->variants as $variant) {
            $matchedColorId = '';
            $matchedSizeId = '';
            foreach ($variant->optionValues as $ov) {
                $optionName = $ov->optionValue->option->name ?? '';
                $val = $ov->optionValue->value ?? '';
                if ($optionName == 'Color') {
                    $color = $colors->firstWhere('name', $val);
                    if($color) { $matchedColorId = $color->id; $selectedColors[] = $color->id; }
                }
                if ($optionName == 'Size') {
                    $size = $sizes->firstWhere('name', $val);
                    if($size) { $matchedSizeId = $size->id; $selectedSizes[] = $size->id; }
                }
            }
            $hash = 'color_'.$matchedColorId.'_size_'.$matchedSizeId;
            $vImage = $variant->images->first();
            $existingVariantsMap[$hash] = [
                'sku' => $variant->sku,
                'price' => $variant->price,
                'stock' => $variant->stock_quantity,
                'image_url' => $vImage ? asset('storage/' . $vImage->image_path) : null,
            ];
        }

        $selectedColors = array_unique($selectedColors);
        $selectedSizes = array_unique($selectedSizes);

        return view('admin.admin_editproduct', compact('product', 'colors', 'sizes', 'existingVariantsMap', 'selectedColors', 'selectedSizes'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(StoreProductRequest $request, $id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $this->productService->update($product, $request->validated());

        return redirect()
            ->route('admin.product')
            ->with('success', 'Product updated successfully!');
    }

    /**
     * Remove the specified product from storage (soft delete).
     */
    public function destroy($id): RedirectResponse
    {
        $product = Product::findOrFail($id);
        $product->delete();

        return redirect()
            ->route('admin.product')
            ->with('success', 'Product deleted successfully!');
    }
}
