<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Auth middleware handles this at route level
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:5000'],
            'is_active'   => ['nullable', 'boolean'],
            'images'      => ['required', 'array', 'max:10'],
            'images.*'    => ['image', 'mimes:jpg,jpeg,png,webp', 'max:2048'], // 2MB each
            'color_ids'   => ['nullable', 'array'],
            'color_ids.*' => ['exists:colors,id'],
            'size_ids'    => ['nullable', 'array'],
            'size_ids.*'  => ['exists:sizes,id'],
            
            'variants'             => ['nullable', 'array'],
            'variants.*.color_id'  => ['nullable', 'exists:colors,id'],
            'variants.*.size_id'   => ['nullable', 'exists:sizes,id'],
            'variants.*.price'     => ['required', 'numeric', 'min:0'],
            'variants.*.stock'     => ['required', 'integer', 'min:0'],
            'variants.*.image'     => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ];
    }

    /**
     * Custom validation messages.
     */
    public function messages(): array
    {
        return [
            'name.required'       => 'Product name is required.',
            'name.max'            => 'Product name may not be greater than 255 characters.',
            'description.required'=> 'Description is required.',
            'description.max'     => 'Description may not be greater than 5000 characters.',
            'images.required'     => 'At least one product image is required.',
            'images.max'          => 'You may not upload more than 10 images.',
            'images.*.image'      => 'Each uploaded file must be an image.',
            'images.*.mimes'      => 'Images must be of type: jpg, jpeg, png, or webp.',
            'images.*.max'        => 'Each image may not be larger than 2MB.',
            'color_ids.*.exists'  => 'Selected color is invalid.',
            'size_ids.*.exists'   => 'Selected size is invalid.',
            'variants.*.price.required' => 'Variant Price is required.',
            'variants.*.stock.required' => 'Variant Stock is required.',
            'variants.*.image.image'    => 'Variant file must be an image.',
            'variants.*.image.mimes'    => 'Variant image must be of type: jpg, jpeg, png, or webp.',
            'variants.*.image.max'      => 'Variant image may not be larger than 2MB.',
        ];
    }
}
