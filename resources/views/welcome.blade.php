<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'Premium Store') }}</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Tailwind -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
</head>

<body class="bg-gray-50 text-gray-900 antialiased selection:bg-black selection:text-white">

    <!-- Navigation -->
    <nav class="fixed top-0 left-0 right-0 z-50 glass-nav transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <div class="flex-shrink-0 flex items-center gap-2">
                    <span class="font-bold text-xl tracking-tight">Product Store</span>
                </div>
                <div class="flex items-center gap-4">
                    @auth
                    <a href="{{ url('/dashboard') }}" class="text-sm font-semibold text-gray-600 hover:text-black transition">Dashboard</a>
                    @else
                    <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-600 hover:text-black transition">Admin Login</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Product Grid -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-28 pb-12">
        <div class="flex items-center justify-between mb-10">
            <div class="flex items-center gap-2">
                <span class="text-sm text-gray-500">{{ $products->total() }} Products</span>
            </div>
        </div>

        @if($products->isEmpty())
        <div class="text-center py-20">
            <div class="inline-block p-6 bg-white rounded-full shadow-sm mb-4">
                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-gray-500">Check back later for new arrivals.</p>
        </div>
        @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-x-8 gap-y-12">
            @foreach($products as $product)
            @php
            // Determine price range
            $prices = $product->variants->pluck('price');
            $minPrice = $prices->min();
            $maxPrice = $prices->max();

            // Extract distinct colors for swatches
            $availableColors = collect();
            $availableSizes = collect();

            foreach($product->variants as $variant) {
            foreach($variant->optionValues as $ov) {
            if($ov->optionValue->option && $ov->optionValue->option->name == 'Color') {
            $availableColors->push($ov->optionValue->value);
            }
            if($ov->optionValue->option && $ov->optionValue->option->name == 'Size') {
            $availableSizes->push($ov->optionValue->value);
            }
            }
            }
            $availableColors = $availableColors->unique();
            $availableSizes = $availableSizes->unique();

            $primaryImage = $product->images->where('is_primary', true)->first() ?? $product->images->first();
            $imageUrl = $primaryImage ? asset('storage/' . $primaryImage->image_path) : 'https://placehold.co/600x800/e2e8f0/94a3b8?text=No+Image';
            @endphp

            <a href="{{ route('product.show', $product->id) }}" class="group product-card relative flex flex-col bg-white rounded-2xl overflow-hidden border border-gray-100">

                <!-- Image Container -->
                <div class="aspect-[4/5] bg-gray-100 relative overflow-hidden">
                    <img src="{{ $imageUrl }}" alt="{{ $product->name }}" class="object-cover w-full h-full group-hover:scale-105 transition-transform duration-700 ease-in-out">

                    @if($product->created_at->diffInDays(now()) < 14)
                        <div class="absolute top-4 left-4 bg-black text-white text-xs font-bold px-3 py-1 mt-1 rounded-full uppercase tracking-wider">
                        New
                </div>
                @endif

                <!-- Quick Add overlay (Desktop) -->
                <div class="absolute bottom-4 left-4 right-4 opacity-0 group-hover:opacity-100 transition-opacity duration-300 translate-y-4 group-hover:translate-y-0">
                    <button class="w-full bg-white/90 backdrop-blur-md text-black font-semibold py-3 rounded-xl border border-gray-100 hover:bg-black hover:text-white transition-colors shadow-lg">
                        Quick View
                    </button>
                </div>
        </div>

        <!-- Product Info -->
        <div class="p-5 flex-1 flex flex-col">
            <div class="flex justify-between items-start">
                <div>
                    <h3 class="text-lg font-bold text-gray-900 line-clamp-1">
                        {{ $product->name }}
                    </h3>
                    <p class="text-sm text-gray-500 mt-1 line-clamp-1">{{ strip_tags($product->description) }}</p>
                </div>
            </div>

            <!-- Price -->
            <div class="mt-3 font-semibold text-gray-900 border-t border-gray-50 pt-3">
                @if($minPrice == $maxPrice && $minPrice > 0)
                ₹{{ number_format($minPrice, 2) }}
                @elseif($minPrice > 0)
                ₹{{ number_format($minPrice, 2) }} - ₹{{ number_format($maxPrice, 2) }}
                @else
                <span class="text-sm text-gray-500">Price Unavailable</span>
                @endif
            </div>

            <!-- Swatches / Meta -->
            <div class="mt-4 pt-3 flex items-center justify-between border-t border-gray-100">
                <div class="flex items-center gap-1.5">
                    @if($availableColors->isNotEmpty())
                    @foreach($availableColors->take(4) as $color)
                    <!-- using generic color blocks to simulate swatches -->
                    <div class="w-4 h-4 rounded-full border border-gray-200" title="{{ $color }}" style="background-color: {{ strtolower(str_replace(' ', '', $color)) }}"></div>
                    @endforeach
                    @if($availableColors->count() > 4)
                    <span class="text-xs text-gray-400 font-medium">+{{ $availableColors->count() - 4 }}</span>
                    @endif
                    @else
                    <span class="text-xs text-gray-400">1 standard variation</span>
                    @endif
                </div>
                <div class="text-xs font-semibold text-gray-400 uppercase tracking-widest">
                    {{ $availableSizes->count() }} Sizes
                </div>
            </div>
        </div>
        </a>
        @endforeach
    </div>

    <div class="mt-12 justify-center">
        {{ $products->links() }}
    </div>
    @endif
    </div>

    <!-- Footer -->
    <footer class="bg-black text-white py-16 mt-20">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-600 text-xs mt-8">&copy; {{ date('Y') }} Premium Store. All rights reserved.</p>
        </div>
    </footer>
</body>

</html>