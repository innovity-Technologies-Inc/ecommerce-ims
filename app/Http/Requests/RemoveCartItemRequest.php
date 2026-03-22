<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class RemoveCartItemRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'cart_id' => 'required|exists:carts,id',
        ];
    }

    public function messages(): array
    {
        return [
            'cart_id.required' => 'Invalid cart item.',
            'cart_id.exists' => 'The selected cart item was not found.',
        ];
    }
}
