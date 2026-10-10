<x-app-layout>
    <div class="py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <form action="{{ route('admin.product.update', $product->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="bg-white dark:bg-gray-800 shadow rounded-lg p-6 mb-6">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white mb-5">
                        1. Edit Product Details
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium mb-2">
                                Product Name <span class="text-red-500">*</span>
                            </label>
                            <input type="text" name="name"
                                value="{{ old('name', $product->name) }}"
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
                                value="{{ old('description', $product->description) }}"
                                placeholder="Enter description"
                                class="w-full border rounded-md shadow-sm {{ $errors->has('description') ? 'border-red-500 bg-red-50' : 'border-gray-300' }}">
                            @error('description')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium mb-2">
                                Product Images (Leave empty to keep existing images)
                            </label>
                            
                            @if($product->images->count() > 0)
                                <div class="mb-3 flex gap-2 flex-wrap">
                                    @foreach($product->images as $img)
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-16 h-16 object-cover border rounded-md" alt="Old Image">
                                    @endforeach
                                </div>
                            @endif

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
                        <div class="md:col-span-2 pt-2 border-t">
                            <label class="flex items-center gap-2 cursor-pointer mt-2 select-none">
                                <input type="hidden" name="is_active" value="0">
                                <input type="checkbox" name="is_active" value="1"
                                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                                    class="w-5 h-5 text-blue-600 rounded border-gray-300 focus:ring-blue-500">
                                <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">Active (Visible in store)</span>
                            </label>
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
                                                {{ in_array($color->id, old('color_ids', $selectedColors)) ? 'checked' : '' }}
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
                                                {{ in_array($size->id, old('size_ids', $selectedSizes)) ? 'checked' : '' }}
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
    
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const colorCheckboxes = document.querySelectorAll('.color-checkbox');
            const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
            const tbody = document.getElementById('variants-tbody');

            // Recover old data if validation fails, layered on top of existing database variants
            const formOldVariants = @json(old('variants', []));
            const oldVariantsMap = @json($existingVariantsMap);

            Object.keys(formOldVariants).forEach(key => {
                let v = formOldVariants[key];
                if (v) {
                    let hash = `color_${v.color_id || ''}_size_${v.size_id || ''}`;
                    if (!oldVariantsMap[hash]) oldVariantsMap[hash] = {};
                    oldVariantsMap[hash].price = v.price || oldVariantsMap[hash].price || '';
                    oldVariantsMap[hash].stock = v.stock || oldVariantsMap[hash].stock || '';
                    oldVariantsMap[hash].sku = v.sku || oldVariantsMap[hash].sku || '';
                }
            });

            // Generate a placeholder SKU
            function generateRandomSKU() {
                return 'VAR-' + Math.random().toString(36).substring(2, 8).toUpperCase();
            }

            function generateVariants() {
                const selectedColors = Array.from(colorCheckboxes).filter(c => c.checked).map(c => ({
                    id: c.dataset.colorId,
                    name: c.dataset.colorName
                }));

                const selectedSizes = Array.from(sizeCheckboxes).filter(s => s.checked).map(s => ({
                    id: s.dataset.sizeId,
                    name: s.dataset.sizeName
                }));

                let combinations = [];

                if (selectedColors.length > 0 && selectedSizes.length > 0) {
                    selectedColors.forEach(color => {
                        selectedSizes.forEach(size => {
                            combinations.push({ color, size });
                        });
                    });
                } else if (selectedColors.length > 0) {
                    selectedColors.forEach(color => combinations.push({ color, size: null }));
                } else if (selectedSizes.length > 0) {
                    selectedSizes.forEach(size => combinations.push({ color: null, size }));
                } else {
                    combinations.push({ color: null, size: null });
                }

                // Preserve existing rows data
                const existingRowsData = {};
                tbody.querySelectorAll('tr').forEach(tr => {
                    const hash = tr.dataset.hash;
                    // Safely query the inputs, handling potential nulls
                    const skuInput = tr.querySelector('input[name$="[sku]"]');
                    const priceInput = tr.querySelector('input[name$="[price]"]');
                    const stockInput = tr.querySelector('input[name$="[stock]"]');
                    const deleted = tr.style.display === 'none'; // Basic delete state check
                    
                    existingRowsData[hash] = {
                        sku: skuInput ? skuInput.value : '',
                        price: priceInput ? priceInput.value : '',
                        stock: stockInput ? stockInput.value : '',
                        deleted: deleted
                    };
                });

                tbody.innerHTML = '';

                combinations.forEach((combo, index) => {
                    const colorName = combo.color ? combo.color.name : '';
                    const sizeName = combo.size ? combo.size.name : '';
                    let variantName = [colorName, sizeName].filter(Boolean).join(' / ');
                    if (!variantName) variantName = 'Default';
                    let hash = `color_${combo.color ? combo.color.id : ''}_size_${combo.size ? combo.size.id : ''}`;

                    let existing = existingRowsData[hash] || oldVariantsMap[hash] || { sku: generateRandomSKU(), price: '', stock: '' };
                    if (existing.deleted) return; // don't show deleted generated rows until hard reset

                    const colorInput = combo.color ? `<input type="hidden" name="variants[${index}][color_id]" value="${combo.color.id}">` : '';
                    const sizeInput = combo.size ? `<input type="hidden" name="variants[${index}][size_id]" value="${combo.size.id}">` : '';

                    const tr = document.createElement('tr');
                    tr.className = 'border-b hover:bg-gray-50';
                    tr.dataset.hash = hash;
                    tr.innerHTML = `
                        <td class="p-3 font-medium text-gray-900 dark:text-gray-100">
                            ${variantName}
                            ${colorInput}
                            ${sizeInput}
                        </td>
                        <td class="p-3">
                            <input type="text" name="variants[${index}][sku]" value="${existing.sku}" readonly class="w-full text-sm bg-gray-100 text-gray-500 border-gray-200 rounded-md shadow-none cursor-not-allowed">
                        </td>
                        <td class="p-3">
                            <input type="number" name="variants[${index}][price]" value="${existing.price}" placeholder="₹0.00" step="0.01" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </td>
                        <td class="p-3">
                            <input type="number" name="variants[${index}][stock]" value="${existing.stock}" placeholder="0" class="w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        </td>
                        <td class="p-3 text-center">
                            ${existing.image_url ? `<div class="mb-1"><img src="${existing.image_url}" class="w-8 h-8 rounded-md object-cover inline-block" alt="thumb"></div>` : ''}
                            <input type="file" name="variants[${index}][image]" accept="image/*" class="w-32 text-xs border border-gray-300 rounded-md hover:cursor-pointer p-1">
                        </td>
                        <td class="p-3 text-center">
                            <button type="button" class="text-red-500 hover:text-red-700 delete-variant-btn" title="Remove">
                                <svg class="w-4 h-4 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                            </button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            }

            colorCheckboxes.forEach(c => c.addEventListener('change', generateVariants));
            sizeCheckboxes.forEach(s => s.addEventListener('change', generateVariants));

            // Delete specific variant row completely (it won't come back on check toggle either since we track delete state) - wait actually, 
            // for simplicity, if they press delete, it just removes the DOM row. If they uncheck and check it comes back blank. That is fine.
            tbody.addEventListener('click', (e) => {
                if (e.target.closest('.delete-variant-btn')) {
                    e.target.closest('tr').remove();
                }
            });

            // Init once if old data exist
            generateVariants();
        });
    </script>
</x-app-layout>