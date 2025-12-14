<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $rules = [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'price' => 'required|numeric|min:0|max:999999.99',
            'category_id' => 'nullable|exists:categories,id',
            'stock_quantity' => 'required|integer|min:0',
            'is_featured' => 'sometimes|boolean',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'variants' => 'sometimes|array',
            'variants.*.variant_type' => 'required_with:variants|string|max:255',
            'variants.*.variant_value' => 'required_with:variants|string|max:255',
            'variants.*.variant_price' => 'nullable|numeric|min:0',
            'variants.*.variant_stock_quantity' => 'nullable|integer|min:0',
        ];

        // Add custom validation for variant stock vs product stock
        $productStock = $this->input('stock_quantity', 0);
        if ($productStock > 0 && $this->has('variants')) {
            $variants = $this->input('variants', []);
            foreach ($variants as $index => $variant) {
                $variantStock = $variant['variant_stock_quantity'] ?? 0;
                if ($variantStock > $productStock) {
                    $rules["variants.{$index}.variant_stock_quantity"] = [
                        'integer',
                        'min:0',
                        function ($attribute, $value, $fail) use ($productStock) {
                            $fail("Variant stock ({$value}) cannot exceed product stock ({$productStock}).");
                        },
                    ];
                }
            }
        }

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Product name is required.',
            'name.max' => 'Product name cannot exceed 255 characters.',
            'price.required' => 'Product price is required.',
            'price.numeric' => 'Product price must be a number.',
            'price.min' => 'Product price cannot be negative.',
            'stock_quantity.required' => 'Stock quantity is required.',
            'stock_quantity.integer' => 'Stock quantity must be an integer.',
            'stock_quantity.min' => 'Stock quantity cannot be negative.',
            'category_id.exists' => 'Selected category does not exist.',
            'variants.array' => 'Variants must be an array.',
            'variants.*.variant_type.required_with' => 'Variant type is required when adding variants.',
            'variants.*.variant_value.required_with' => 'Variant value is required when adding variants.',
            'variants.*.variant_price.numeric' => 'Variant price must be a number.',
            'variants.*.variant_stock_quantity.integer' => 'Variant stock quantity must be an integer.',
            'variants.*.variant_stock_quantity.min' => 'Variant stock quantity cannot be negative.',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param  \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $productStock = $this->input('stock_quantity', 0);
            $variants = $this->input('variants', []);

            foreach ($variants as $index => $variant) {
                $variantStock = $variant['variant_stock_quantity'] ?? 0;
                if ($variantStock > $productStock) {
                    $validator->errors()->add(
                        "variants.{$index}.variant_stock_quantity",
                        "Variant stock ({$variantStock}) cannot exceed product stock ({$productStock})."
                    );
                }
            }
        });
    }
}

