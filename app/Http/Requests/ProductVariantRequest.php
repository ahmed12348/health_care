<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductVariantRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'variant_type' => 'required|string|max:100',
            'variant_value' => 'required|string|max:100',
            'variant_price' => 'nullable|numeric|min:0|max:999999.99',
            'variant_stock_quantity' => 'required|integer|min:0',
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product ID is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'variant_type.required' => 'Variant type is required.',
            'variant_type.max' => 'Variant type cannot exceed 100 characters.',
            'variant_value.required' => 'Variant value is required.',
            'variant_value.max' => 'Variant value cannot exceed 100 characters.',
            'variant_price.numeric' => 'Variant price must be a number.',
            'variant_price.min' => 'Variant price cannot be negative.',
            'variant_stock_quantity.required' => 'Variant stock quantity is required.',
            'variant_stock_quantity.integer' => 'Variant stock quantity must be an integer.',
            'variant_stock_quantity.min' => 'Variant stock quantity cannot be negative.',
        ];
    }
}

