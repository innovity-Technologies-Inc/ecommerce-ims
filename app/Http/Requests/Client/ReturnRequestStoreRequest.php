<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequestStoreRequest extends FormRequest
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
            'order_id' => 'required|string|exists:orders,order_id',
            'order_id_pk' => 'required|integer|exists:orders,id',
            'reason' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'image|max:2048',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|integer|exists:products,id',
            'items.*.product_variant_id' => 'nullable|integer|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:0',
            'items.*.unit_price' => 'required|numeric',
        ];
    }

    public function messages(): array
    {
        return [
            'order_id.required' => 'Invalid Order ID.',
            'reason.required' => 'Please provide a reason for the return.',
            'images.*.image' => 'One or more proof files are not valid images.',
            'images.*.max' => 'One or more proof images exceed the 2MB size limit. Please compress and try again.',
            'items.required' => 'Please select at least one item to return.',
            'items.*.quantity.required' => 'Please specify the return quantity.',
        ];
    }

    public function attributes(): array
    {
        return [
            'images' => 'Return Images',
            'images.*' => 'Return Image',
        ];
    }
}
