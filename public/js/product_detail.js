document.addEventListener("DOMContentLoaded", () => {
    const defaultImage = window.productConfig.defaultImage;
    const variants = window.productConfig.variants;

    const colorRadios = document.querySelectorAll('input[name="color_select"]');
    const sizeRadios = document.querySelectorAll('input[name="size_select"]');

    const priceEl = document.getElementById("productPrice");
    const stockEl = document.getElementById("stockStatus");
    const btnEl = document.getElementById("addToCartBtn");
    const imgEl = document.getElementById("mainImage");

    const colorLabel = document.getElementById("selectedColorLabel");
    const sizeLabel = document.getElementById("selectedSizeLabel");

    /**
     * Get the value of the checked radio button from a given list/group.
     */
    function getSelectedValue(radios) {
        const checked = Array.from(radios).find((r) => r.checked);
        return checked ? checked.value : null;
    }

    function updateUI() {
        const color = getSelectedValue(colorRadios);
        const size = getSelectedValue(sizeRadios);

        if (colorLabel)
            colorLabel.textContent = color ? color : "Select a color";
        if (sizeLabel) sizeLabel.textContent = size ? size : "Select a size";

        // Find matching variant
        let match = null;

        if (colorRadios.length > 0 && sizeRadios.length > 0) {
            if (color && size)
                match = variants.find(
                    (v) => v.color === color && v.size === size,
                );
        } else if (colorRadios.length > 0) {
            if (color)
                match = variants.find((v) => v.color === color && !v.size);
        } else if (sizeRadios.length > 0) {
            if (size) match = variants.find((v) => !v.color && v.size === size);
        } else {
            match = variants.length > 0 ? variants[0] : null; // Simple products
        }

        if (match) {
            // Update Price
            priceEl.textContent = "₹" + match.price;

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
                btnEl.classList.remove(
                    "bg-gray-200",
                    "text-gray-400",
                    "disabled:cursor-not-allowed",
                );
                btnEl.classList.add("bg-black", "text-white");
                btnEl.innerHTML = `
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                Add to Bag`;
            } else {
                stockEl.innerHTML = `<span class="text-red-500">Out of Stock</span> (SKU: ${match.sku})`;
                btnEl.disabled = true;
                btnEl.classList.add(
                    "bg-gray-200",
                    "text-gray-400",
                    "disabled:cursor-not-allowed",
                );
                btnEl.classList.remove("bg-black", "text-white");
                btnEl.innerHTML = `Sold Out`;
            }
        } else {
            if (color || size) {
                stockEl.innerHTML =
                    '<span class="text-gray-400">Unavailable Combination</span>';
            }
            btnEl.disabled = true;
            btnEl.classList.add(
                "bg-gray-200",
                "text-gray-400",
                "disabled:cursor-not-allowed",
            );
            btnEl.classList.remove("bg-black", "text-white");
            btnEl.innerHTML = `Select Options`;
        }
    }

    colorRadios.forEach((r) => r.addEventListener("change", updateUI));
    sizeRadios.forEach((r) => r.addEventListener("change", updateUI));

    // If it's a simple product with no options, initialize button state
    if (colorRadios.length === 0 && sizeRadios.length === 0) {
        updateUI();
    }
});
