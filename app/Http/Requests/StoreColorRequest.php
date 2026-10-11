<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreColorRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'color_name'     => [
                'required',
                'string',
                'max:100',
                Rule::unique('colors', 'name')->whereNull('deleted_at'),
            ],
            'color_hex_code' => ['nullable', 'regex:/^#([A-Fa-f0-9]{6})$/'],
        ];
    }

    public function messages(): array
    {
        return [
            'color_name.required' => 'Color name is required.',
            'color_name.max'      => 'Color name may not be greater than 100 characters.',
            'color_name.unique'   => 'This color already exists.',
            'color_hex_code.regex' => 'Hex code must be a valid format like #FF0000.',
        ];
    }

    /**
     * Handle a failed validation attempt.
     */
    protected function failedValidation(\Illuminate\Contracts\Validation\Validator $validator)
    {
        if ($this->is('api/*') || $this->expectsJson()) {
            throw new \Illuminate\Http\Exceptions\HttpResponseException(
                response()->json([
                    'success' => false,
                    'message' => 'Validation error',
                    'errors'  => $validator->errors(),
                ], 422)
            );
        }

        parent::failedValidation($validator);
    }
}
