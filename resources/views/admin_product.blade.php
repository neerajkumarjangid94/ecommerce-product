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
    <div class="py-12 bg-gray-50 min-h-screen dark:bg-gray-900">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            {{-- Main Content --}}
            @if($products->isEmpty())
                <div class="flex flex-col justify-center items-center h-64 bg-white dark:bg-gray-800 rounded-3xl shadow-sm border border-gray-100 dark:border-gray-750">
                    <div class="p-4 bg-blue-50 dark:bg-blue-900/30 rounded-full mb-4">
                        <svg class="h-12 w-12 text-blue-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No products found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by creating a new product.</p>
                </div>
            @else
                <div class="grid grid-cols-1 gap-8">
                    @foreach ($products as $product)
                        @php
                            $isSimple = $product->options->isEmpty();
                            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
                            $imagePath = $primaryImage ? Storage::url($primaryImage->image_path) : 'https://images.unsplash.com/photo-1560393464-5c69a73c5770?ixlib=rb-1.2.1&auto=format&fit=crop&w=500&q=60';
                        @endphp

                        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 border border-gray-100 dark:border-gray-750 overflow-hidden transform group hover:-translate-y-1">
                            <div class="flex flex-col md:flex-row">
                                {{-- Product Image Side --}}
                                <div class="md:w-64 h-64 md:h-auto relative overflow-hidden bg-gray-100 dark:bg-gray-900">
                                    <img class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-700 ease-in-out" src="{{ $imagePath }}" alt="{{ $product->name }}">
                                    @if($product->is_active)
                                        <div class="absolute top-4 left-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-500/90 text-white backdrop-blur-sm shadow flex gap-1">
                                                <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                                                Active
                                            </span>
                                        </div>
                                    @else
                                        <div class="absolute top-4 left-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-red-500/90 text-white backdrop-blur-sm shadow">
                                                Inactive
                                            </span>
                                        </div>
                                    @endif
                                </div>

                                {{-- Product Details Side --}}
                                <div class="flex-1 p-6 md:p-8 flex flex-col justify-between">
                                    <div>
                                        <div class="flex items-center justify-between mb-2">
                                            <h3 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $product->name }}</h3>
                                            @if($isSimple)
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400 border border-indigo-100 dark:border-indigo-800">
                                                    Simple Product
                                                </span>
                                            @else
                                                <span class="px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-900/30 dark:text-purple-400 border border-purple-100 dark:border-purple-800">
                                                    {{ $product->variants->count() }} Variants
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600 dark:text-gray-400 line-clamp-2 leading-relaxed">
                                            {{ $product->description }}
                                        </p>
                                    </div>

                                    <div class="mt-6 pt-6 border-t border-gray-100 dark:border-gray-700">
                                        @if($isSimple && $product->variants->isNotEmpty())
                                            @php $variant = $product->variants->first(); @endphp
                                            <div class="flex items-end justify-between">
                                                <div>
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Price</p>
                                                    <p class="text-2xl font-black text-gray-900 dark:text-white tracking-tight">₹{{ number_format($variant->price, 2) }}</p>
                                                </div>
                                                <div class="text-right">
                                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">In Stock</p>
                                                    <p class="text-lg font-bold {{ $variant->stock_quantity > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }}">
                                                        {{ $variant->stock_quantity }} <span class="text-sm font-medium text-gray-400">units</span>
                                                    </p>
                                                </div>
                                            </div>
                                        @elseif(!$isSimple && $product->variants->isNotEmpty())
                                            {{-- Variants Mini Gallery / List --}}
                                            <div class="mt-2 text-sm text-gray-800 dark:text-gray-200">
                                                <div class="font-semibold mb-4 text-gray-900 dark:text-gray-100 flex items-center">
                                                    <svg class="w-5 h-5 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                                    Available Variants
                                                </div>
                                                <div class="flex flex-wrap gap-4">
                                                    @foreach($product->variants as $variant)
                                                        @php
                                                            $variantImage = $variant->images->first();
                                                            $vImagePath = $variantImage ? Storage::url($variantImage->image_path) : null;
                                                            $optionsStr = $variant->optionValues->map(fn($ov) => $ov->optionValue->value ?? '-')->join(' / ') ?: 'Default';
                                                        @endphp
                                                        <div class="relative flex items-center bg-gray-50 dark:bg-gray-750 border border-gray-200 dark:border-gray-700 rounded-xl p-2 pr-4 shadow-sm hover:shadow-md hover:border-blue-300 dark:hover:border-blue-500 transition-all cursor-default group/var max-w-sm">
                                                            @if($vImagePath)
                                                                <div class="h-12 w-12 rounded-lg overflow-hidden bg-gray-200 flex-shrink-0 mr-3">
                                                                    <img src="{{ $vImagePath }}" class="h-full w-full object-cover">
                                                                </div>
                                                            @else
                                                                <div class="h-12 w-12 rounded-lg bg-gray-200 dark:bg-gray-800 flex items-center justify-center flex-shrink-0 mr-3">
                                                                    <span class="text-xs text-gray-400 font-bold text-center">No Img</span>
                                                                </div>
                                                            @endif
                                                            <div class="flex flex-col justify-center">
                                                                <span class="text-sm font-bold text-gray-900 dark:text-white">{{ $optionsStr }}</span>
                                                                <div class="flex items-center gap-2 mt-0.5">
                                                                    <span class="text-xs font-semibold text-blue-600 dark:text-blue-400">₹{{ number_format($variant->price, 2) }}</span>
                                                                    <span class="text-gray-300 dark:text-gray-600">•</span>
                                                                    <span class="text-xs {{ $variant->stock_quantity > 0 ? 'text-gray-500 dark:text-gray-400' : 'text-red-500 font-medium' }}">{{ $variant->stock_quantity }} left</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @else
                                            <div class="text-center p-4 bg-gray-50 dark:bg-gray-750 rounded-xl border border-dashed border-gray-300 dark:border-gray-700">
                                                <span class="text-gray-500 dark:text-gray-400 text-sm">No variants or pricing details mapped yet.</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

        </div>
    </div>
</x-app-layout>