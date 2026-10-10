document.addEventListener('DOMContentLoaded', () => {
    const colorCheckboxes = document.querySelectorAll('.color-checkbox');
    const sizeCheckboxes = document.querySelectorAll('.size-checkbox');
    const tbody = document.getElementById('variants-tbody');

    // Recover old data if validation fails
    const oldVariants = window.oldVariantsData || [];
    const oldVariantsMap = {};
    Object.keys(oldVariants).forEach(key => {
        let v = oldVariants[key];
        if (v) {
            let hash = `color_${v.color_id || ''}_size_${v.size_id || ''}`;
            oldVariantsMap[hash] = v;
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
                    combinations.push({
                        color,
                        size
                    });
                });
            });
        } else if (selectedColors.length > 0) {
            selectedColors.forEach(color => combinations.push({
                color,
                size: null
            }));
        } else if (selectedSizes.length > 0) {
            selectedSizes.forEach(size => combinations.push({
                color: null,
                size
            }));
        } else {
            combinations.push({
                color: null,
                size: null
            });
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

            let existing = existingRowsData[hash] || oldVariantsMap[hash] || {
                sku: generateRandomSKU(),
                price: '',
                stock: ''
            };
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
