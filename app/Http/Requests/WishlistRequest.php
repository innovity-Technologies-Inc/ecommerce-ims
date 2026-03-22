<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class WishlistRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'exists:products,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'product_id.required' => 'Please select a product to add to your wishlist.',
            'product_id.exists' => 'The selected product does not exist.',
        ];
    }
}
