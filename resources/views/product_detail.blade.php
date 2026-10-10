<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $product->name }} | {{ config('app.name', 'Premium Store') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        body { font-family: 'Outfit', sans-serif; }
        .glass-nav { 
            background: rgba(255, 255, 255, 0.7); 
            backdrop-filter: blur(12px); 
            -webkit-backdrop-filter: blur(12px); 
            border-bottom: 1px solid rgba(0,0,0,0.05);
        }
    </style>
</head>
<body class="bg-gray-50 text-gray-900 antialiased selection:bg-black selection:text-white pb-20">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <a href="{{ route('home') }}" class="flex-shrink-0 flex items-center gap-2 hover:opacity-80 transition-opacity">
                    <span class="font-bold text-xl tracking-tight">Product Store</span>
                </a>
                <div class="flex items-center gap-4">
                    <a href="{{ route('home') }}" class="text-sm font-semibold text-gray-600 hover:text-black transition">Back to Store</a>
                </div>
            </div>
        </div>
    </nav>

    @php
        $variantsData = $product->variants->map(function($variant) {
            $color = null;
            $size = null;
            foreach($variant->optionValues as $ov) {
                if($ov->optionValue->option && $ov->optionValue->option->name == 'Color') {
                    $color = $ov->optionValue->value;
                }
                if($ov->optionValue->option && $ov->optionValue->option->name == 'Size') {
                    $size = $ov->optionValue->value;
                }
            }
            $img = $variant->images->where('is_primary', true)->first() ?? $variant->images->first();
            return [
                'id' => $variant->id,
                'sku' => $variant->sku,
                'price' => number_format($variant->price, 2),
                'stock' => $variant->stock_quantity,
                'color' => $color,
                'size' => $size,
                'image' => $img ? asset('storage/' . $img->image_path) : null,
            ];
        });

        $primaryProductImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
        $defaultImage = $primaryProductImage ? asset('storage/' . $primaryProductImage->image_path) : 'https://placehold.co/600x800/e2e8f0/94a3b8?text=No+Image';

        $availableColors = $variantsData->pluck('color')->filter()->unique()->values();
        $availableSizes = $variantsData->pluck('size')->filter()->unique()->values();
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 pt-28">
        <div class="grid grid-cols-1 md:grid-cols-[400px_1fr] gap-10 lg:gap-12">
            
            <!-- Left: Images -->
            <div class="flex flex-col w-full">
                <div class="w-full bg-gray-50 rounded-xl overflow-hidden aspect-[4/5] relative border border-gray-100">
                    <img id="mainImage" src="{{ $defaultImage }}" class="object-contain w-full h-full p-2 transition-transform duration-500 hover:scale-105" alt="{{ $product->name }}">
                </div>
                <!-- Thumbnails (Optional: include variant images or product extra images) -->
                @if($product->images->count() > 1 || $variantsData->whereNotNull('image')->count() > 0)
                    <div class="mt-3 flex gap-3 overflow-x-auto pb-2 snap-x w-full">
                        <button onclick="document.getElementById('mainImage').src='{{ $defaultImage }}'" class="flex-shrink-0 w-20 h-24 rounded-lg overflow-hidden border border-gray-200 hover:border-black focus:border-black transition-colors focus:outline-none">
                            <img src="{{ $defaultImage }}" class="object-cover w-full h-full" alt="Main Thumb">
                        </button>
                        
                        @foreach($product->images->where('is_primary', false) as $extraImage)
                            <button onclick="document.getElementById('mainImage').src='{{ asset('storage/' . $extraImage->image_path) }}'" class="flex-shrink-0 w-20 h-24 rounded-lg overflow-hidden border border-gray-200 hover:border-black focus:border-black transition-colors focus:outline-none">
                                <img src="{{ asset('storage/' . $extraImage->image_path) }}" class="object-cover w-full h-full" alt="Extra Thumb">
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Right: Details -->
            <div class="flex flex-col pt-2">
                <h1 class="text-2xl sm:text-3xl font-bold tracking-tight text-gray-900 mb-1">{{ $product->name }}</h1>
                <div class="text-xl font-semibold text-gray-900 mb-5" id="productPrice">
                    @if($variantsData->isEmpty())
                        ₹0.00
                    @elseif($variantsData->min('price') == $variantsData->max('price'))
                        ₹{{ $variantsData->min('price') }}
                    @else
                        ₹{{ $variantsData->min('price') }} - ₹{{ $variantsData->max('price') }}
                    @endif
                </div>

                <div class="prose prose-sm text-gray-600 mb-6 border-b border-gray-100 pb-6">
                    {!! nl2br(e($product->description)) !!}
                </div>

                @if($variantsData->isNotEmpty())
                    <!-- Variant Selectors -->
                    <div class="space-y-5">
                        @if($availableColors->isNotEmpty())
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-widest">Color</h3>
                                    <span class="text-xs text-gray-500" id="selectedColorLabel">Select a color</span>
                                </div>
                                <div class="flex flex-wrap gap-2.5">
                                    @foreach($availableColors as $color)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="color_select" value="{{ $color }}" class="peer sr-only">
                                            <div class="w-8 h-8 rounded-full border-2 border-transparent peer-checked:border-black peer-checked:scale-110 transition-all shadow-sm focus:outline-none ring-2 ring-transparent peer-focus:ring-gray-300 flex items-center justify-center p-[1px]" title="{{ $color }}">
                                                <div class="w-full h-full rounded-full border border-gray-200" style="background-color: {{ strtolower(str_replace(' ', '', $color)) }}"></div>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        @if($availableSizes->isNotEmpty())
                            <div>
                                <div class="flex items-center justify-between mb-2">
                                    <h3 class="text-xs font-semibold text-gray-900 uppercase tracking-widest">Size</h3>
                                    <span class="text-xs text-gray-500" id="selectedSizeLabel">Select a size</span>
                                </div>
                                <div class="flex flex-wrap gap-2">
                                    @foreach($availableSizes as $size)
                                        <label class="cursor-pointer">
                                            <input type="radio" name="size_select" value="{{ $size }}" class="peer sr-only">
                                            <div class="px-4 py-1.5 rounded-md border border-gray-200 text-sm font-medium text-gray-900 bg-white peer-checked:border-black peer-checked:bg-black peer-checked:text-white hover:bg-gray-50 transition-all select-none">
                                                {{ $size }}
                                            </div>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <div class="mt-6 space-y-3">
                    <div id="stockStatus" class="text-xs font-medium text-gray-500 mb-1"></div>
                    <button id="addToCartBtn" disabled class="w-full bg-gray-200 text-gray-400 py-3 px-6 rounded-lg font-bold text-sm tracking-wide hover:bg-black hover:text-white transition-colors duration-300 flex items-center justify-center gap-2 disabled:cursor-not-allowed">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <span>Select Options</span>
                    </button>
                    <p class="text-xs text-gray-400 text-center mt-2">Free standard shipping on orders over ₹5,000</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Javascript to handle swatches -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const defaultImage = "{{ $defaultImage }}";
            const variants = @json($variantsData);
            
            const colorRadios = document.querySelectorAll('input[name="color_select"]');
            const sizeRadios = document.querySelectorAll('input[name="size_select"]');
            
            const priceEl = document.getElementById('productPrice');
            const stockEl = document.getElementById('stockStatus');
            const btnEl = document.getElementById('addToCartBtn');
            const imgEl = document.getElementById('mainImage');
            
            const colorLabel = document.getElementById('selectedColorLabel');
            const sizeLabel = document.getElementById('selectedSizeLabel');

            function getSelectedValue(radios) {
                const checked = Array.from(radios).find(r => r.checked);
                return checked ? checked.value : null;
            }

            function updateUI() {
                const color = getSelectedValue(colorRadios);
                const size = getSelectedValue(sizeRadios);

                if(colorLabel) colorLabel.textContent = color ? color : 'Select a color';
                if(sizeLabel) sizeLabel.textContent = size ? size : 'Select a size';

                // Find matching variant
                let match = null;

                if (colorRadios.length > 0 && sizeRadios.length > 0) {
                    if (color && size) match = variants.find(v => v.color === color && v.size === size);
                } else if (colorRadios.length > 0) {
                    if (color) match = variants.find(v => v.color === color && !v.size);
                } else if (sizeRadios.length > 0) {
                    if (size) match = variants.find(v => !v.color && v.size === size);
                } else {
                    match = variants.length > 0 ? variants[0] : null; // Simple products
                }

                if (match) {
                    // Update Price
                    priceEl.textContent = '₹' + match.price;
                    
                    // Update Image
                    if (match.image) {
                        imgEl.src = match.image;
                    } else {
                        imgEl.src = defaultImage;
                    }

                    // Update Stock & Button
                    if (match.stock > 0) {
                        stockEl.innerHTML = `<span class="text-green-600">In Stock</span> (SKU: ${match.sku})`;
                        btnEl.disabled = false;
                        btnEl.classList.remove('bg-gray-200', 'text-gray-400', 'disabled:cursor-not-allowed');
                        btnEl.classList.add('bg-black', 'text-white');
                        btnEl.innerHTML = `
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        Add to Bag`;
                    } else {
                        stockEl.innerHTML = `<span class="text-red-500">Out of Stock</span> (SKU: ${match.sku})`;
                        btnEl.disabled = true;
                        btnEl.classList.add('bg-gray-200', 'text-gray-400', 'disabled:cursor-not-allowed');
                        btnEl.classList.remove('bg-black', 'text-white');
                        btnEl.innerHTML = `Sold Out`;
                    }
                } else {
                    if (color || size) {
                        stockEl.innerHTML = '<span class="text-gray-400">Unavailable Combination</span>';
                    }
                    btnEl.disabled = true;
                    btnEl.classList.add('bg-gray-200', 'text-gray-400', 'disabled:cursor-not-allowed');
                    btnEl.classList.remove('bg-black', 'text-white');
                    btnEl.innerHTML = `Select Options`;
                }
            }

            colorRadios.forEach(r => r.addEventListener('change', updateUI));
            sizeRadios.forEach(r => r.addEventListener('change', updateUI));

            // If it's a simple product with no options, initialize button state
            if(colorRadios.length === 0 && sizeRadios.length === 0) {
                updateUI();
            }
        });
    </script>
</body>
</html>
