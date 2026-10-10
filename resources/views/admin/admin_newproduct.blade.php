<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('admin.product.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-5">
                        1. Product Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                value="{{ old('name') }}"
                                placeholder="Enter product name"
                                class="w-full border rounded-md shadow-sm {{ $errors->has('name') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            @error('name')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Description <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="description"
                                value="{{ old('description') }}"
                                placeholder="Enter description"
                                class="w-full border rounded-md shadow-sm {{ $errors->has('description') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            @error('description')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">
                                Product Images <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="images[]"
                                multiple accept="image/*"
                                class="w-full rounded-md border p-2 {{ $errors->has('images') || $errors->has('images.*') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            @error('images')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            @error('images.*')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- PRODUCT OPTIONS --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <div class="flex items-center justify-between mb-5 border-b border-gray-100 pb-3">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            2. Product Options
                        </h3>
                        <a href="{{ route('admin.settings') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 hover:underline">
                            Manage Colors & Sizes
                        </a>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

                        {{-- Colors --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                🎨 Colors
                            </label>
                            @if ($colors->isEmpty())
                            <p class="text-sm text-gray-400 border border-dashed border-gray-200 rounded-lg p-3">
                                No colors found.
                                <a href="{{ route('admin.settings') }}" class="text-blue-500 hover:underline">Add from Settings →</a>
                            </p>
                            @else
                            <div class="flex flex-wrap gap-3">
                                @foreach ($colors as $color)
                                <label class="flex items-center gap-2 cursor-pointer select-none group">
                                    <input type="checkbox"
                                        name="color_ids[]"
                                        value="{{ $color->id }}"
                                        data-color-id="{{ $color->id }}"
                                        data-color-name="{{ $color->name }}"
                                        {{ in_array($color->id, old('color_ids', [])) ? 'checked' : '' }}
                                        class="color-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="w-5 h-5 rounded-full border border-gray-300 inline-block flex-shrink-0"
                                        style="background-color: {{ $color->hex_code ?? '#ccc' }}"></span>
                                    <span class="text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600">
                                        {{ $color->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        {{-- Sizes --}}
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">
                                📐 Sizes
                            </label>
                            @if ($sizes->isEmpty())
                            <p class="text-sm text-gray-400 border border-dashed border-gray-200 rounded-lg p-3">
                                No sizes found.
                                <a href="{{ route('admin.settings') }}" class="text-blue-500 hover:underline">Add from Settings →</a>
                            </p>
                            @else
                            <div class="flex flex-wrap gap-3">
                                @foreach ($sizes as $size)
                                <label class="flex items-center gap-2 cursor-pointer select-none group">
                                    <input type="checkbox"
                                        name="size_ids[]"
                                        value="{{ $size->id }}"
                                        data-size-id="{{ $size->id }}"
                                        data-size-name="{{ $size->name }}"
                                        {{ in_array($size->id, old('size_ids', [])) ? 'checked' : '' }}
                                        class="size-checkbox w-4 h-4 rounded border-gray-300 text-blue-600 focus:ring-blue-500">
                                    <span class="text-sm font-medium px-2 py-0.5 bg-gray-100 rounded border border-gray-300 text-gray-700 group-hover:border-blue-500 group-hover:text-blue-600">
                                        {{ $size->name }}
                                    </span>
                                </label>
                                @endforeach
                            </div>
                            @endif
                        </div>

                    </div>
                </div>

                {{-- PRODUCT VARIANTS --}}
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <div class="mb-5">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-white">
                            3. Product Variants
                        </h3>
                        @if ($errors->has('variants.*'))
                        <div class="p-3 mt-3 text-sm text-red-600 bg-red-50 border border-red-200 rounded-md">
                            ⚠️ Please ensure all variants have a valid <strong>Price</strong> and <strong>Stock</strong>.
                        </div>
                        @endif
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="p-3">VARIANT</th>
                                    <th class="p-3">SKU</th>
                                    <th class="p-3">PRICE</th>
                                    <th class="p-3">STOCK</th>
                                    <th class="p-3 text-center">IMAGE</th>
                                    <th class="p-3 text-center"></th>
                                </tr>
                            </thead>
                            <tbody id="variants-tbody">
                            </tbody>
                        </table>
                    </div>

                    <p class="text-sm text-gray-500 mt-3">
                        Variants will be generated from the selected option values.
                    </p>
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-3">
                    <a href="{{ url()->previous() }}"
                        class="px-5 py-2 border rounded-md text-gray-700">
                        Cancel
                    </a>

                    <button type="submit"
                        class="px-5 py-2 rounded-md text-white font-semibold"
                        style="background-color: #2563eb;">
                        Save Product
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- Blade Variables For JS -->
    <script>
        window.oldVariantsData = @json(old('variants', []));
    </script>
    <script src="{{ asset('js/admin_newproduct.js') }}"></script>
</x-app-layout>