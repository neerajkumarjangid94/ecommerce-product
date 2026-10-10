<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">
                Admin Product
            </h2>

            <a href="{{ route('admin.newproduct') }}"
                style="background-color: #2563eb; color: white;"
                class="px-4 py-2 rounded-md font-semibold text-sm hover:opacity-90">
                + Add New Product
            </a>
        </div>
    </x-slot>

    <div class="bg-white min-h-screen">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            
            @if($products->isEmpty())
                <div class="text-center py-20">
                    <p class="text-gray-500">No products found. Start by adding one.</p>
                </div>
            @else
                <div class="flex flex-col gap-12">
                    @foreach ($products as $product)
                        @php
                            $isSimple = $product->options->isEmpty();
                            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                            $imagePath = $primaryImage ? asset('storage/' . $primaryImage->image_path) : 'https://placehold.co/400';
                            
                            $firstVariant = $product->variants->first();
                            $displayPrice = $firstVariant ? $firstVariant->price : 0;
                            $displaySku = $firstVariant ? $firstVariant->sku : 'N/A';
                            
                            $variantCount = $product->variants->count();
                        @endphp
                        
                        <div class="bg-white {{ !$loop->last ? 'border-b-2 border-gray-100 pb-12' : '' }}">
                            {{-- Product Header Section --}}
                            <div class="flex flex-row items-start gap-4 sm:gap-6 mb-8">
                                {{-- Main Image --}}
                                <div class="flex-shrink-0 border border-gray-200 rounded-2xl overflow-hidden bg-white" style="width: 200px; height: 200px; flex: 0 0 auto;">
                                    <img src="{{ $imagePath }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                                </div>
                                
                                {{-- Product Info --}}
                                <div class="flex flex-col py-1">
                                    <h3 class="text-2xl font-semibold text-gray-900 leading-tight">{{ $product->name }}</h3>
                                    <p class="text-[15px] text-gray-500 mt-1">SKU: {{ $displaySku }}</p>
                                    <p class="text-2xl font-medium text-gray-900 mt-2">₹{{ number_format($displayPrice, 0) }}</p>
                                    
                                    <div class="flex items-center gap-2 mt-4">
                                        @if(!$isSimple && $variantCount > 0)
                                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                                {{ $variantCount }} Variants
                                            </span>
                                        @endif
                                        @if($product->is_active)
                                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-green-100/60 text-green-700">
                                                Active
                                            </span>
                                        @else
                                            <span class="px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-700">
                                                Inactive
                                            </span>
                                        @endif
                                    </div>
                                    
                                    {{-- Actions --}}
                                    <div class="flex items-center gap-3 mt-4">
                                        <a href="{{ route('admin.product.edit', $product->id) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-full text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                            <svg class="w-4 h-4 mr-2 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"></path></svg>
                                            Edit
                                        </a>
                                        <form action="{{ route('admin.product.destroy', $product->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Are you sure you want to delete this product? All variants will also be deleted.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="inline-flex items-center px-4 py-2 border border-red-300 rounded-full text-sm font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                                                <svg class="w-4 h-4 mr-2 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                Delete
                                            </button>
                                        </form>
                                        {{-- Intentionally skipping "View Details" solid button as requested --}}
                                    </div>
                                </div>
                            </div>
                            
                            {{-- Variants Section --}}
                            @if(!$isSimple && $variantCount > 0)
                                <hr class="my-6 border-gray-200">
                                
                                <h4 class="text-xl font-semibold text-gray-900 mb-4 tracking-tight">Variant Images</h4>
                                
                                <div class="flex flex-nowrap overflow-x-auto gap-4 pb-4 snap-x">
                                    @foreach($product->variants as $variant)
                                        @php
                                            $variantImage = $variant->images->first();
                                            $vImagePath = $variantImage ? asset('storage/' . $variantImage->image_path) : 'https://placehold.co/500';
                                            $optionsStr = $variant->optionValues->map(fn($ov) => $ov->optionValue->value ?? '-')->join(' / ') ?: 'Default';
                                        @endphp
                                        
                                        <div class="flex flex-col flex-shrink-0 snap-start" style="width: 150px; flex: 0 0 auto;">
                                            <div class="aspect-square border border-gray-200 rounded-2xl overflow-hidden bg-white mb-3" style="width: 150px; height: 150px;">
                                                <img src="{{ $vImagePath }}" alt="{{ $optionsStr }}" class="w-full h-full object-cover p-0">
                                            </div>
                                            <div>
                                                <h5 class="text-[15px] font-semibold text-gray-900 truncate" title="{{ $optionsStr }}">
                                                    {{ $optionsStr }}
                                                </h5>
                                                <p class="text-[14px] text-gray-500 mt-0.5">
                                                    ₹{{ number_format($variant->price, 0) }} &middot; Stock {{ $variant->stock_quantity }}
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>