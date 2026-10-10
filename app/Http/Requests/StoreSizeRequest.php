<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreSizeRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'size_name' => [
                'required', 'string', 'max:50',
                Rule::unique('sizes', 'name')->whereNull('deleted_at'),
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'size_name.required' => 'Size name is required.',
            'size_name.max'      => 'Size name may not be greater than 50 characters.',
            'size_name.unique'   => 'This size already exists.',
        ];
    }
}
