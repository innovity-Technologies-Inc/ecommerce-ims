<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CartRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product to add to your cart.',
            'product_id.exists' => 'The selected product does not exist.',
            'product_variant_id.exists' => 'The selected product variant is invalid.',
            'quantity.required' => 'Please specify the quantity.',
            'quantity.integer' => 'The quantity must be a whole number.',
            'quantity.min' => 'You must add at least one item to the cart.',
        ];
    }
}
