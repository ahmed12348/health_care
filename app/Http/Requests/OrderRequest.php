<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class OrderRequest extends FormRequest
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
            'user_id' => 'nullable|exists:users,id',
            'customer_name' => 'nullable|string|max:255',
            'customer_email' => 'nullable|email|max:255',
            'customer_phone' => 'nullable|string|max:50',
            'customer_address' => 'nullable|string|max:500',
            'order_notes' => 'nullable|string|max:1000',
            'total_price' => 'nullable|numeric|min:0|max:999999.99',
            'total_points_earned' => 'sometimes|integer|min:0',
            'total_points_spent' => 'sometimes|integer|min:0',
            'order_status' => 'required|string|in:pending,processing,completed,cancelled',
            'products' => 'sometimes|array|min:1',
            'products.*.product_id' => 'required_with:products|exists:products,id',
            'products.*.variant_id' => 'nullable|exists:product_variants,id',
            'products.*.quantity' => 'required_with:products|integer|min:1',
            'products.*.price' => 'nullable|numeric|min:0',
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
            'user_id.exists' => 'Selected user does not exist.',
            'total_price.required' => 'Total price is required.',
            'total_price.numeric' => 'Total price must be a number.',
            'total_price.min' => 'Total price cannot be negative.',
            'order_status.required' => 'Order status is required.',
            'order_status.in' => 'Invalid order status.',
            'products.array' => 'Products must be an array.',
            'products.min' => 'Order must have at least one product.',
            'products.*.product_id.required_with' => 'Product ID is required for each product.',
            'products.*.product_id.exists' => 'One or more products do not exist.',
            'products.*.quantity.required_with' => 'Quantity is required for each product.',
            'products.*.quantity.min' => 'Quantity must be at least 1.',
        ];
    }
}

