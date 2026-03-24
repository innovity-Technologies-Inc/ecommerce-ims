<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FlashSaleRequest extends FormRequest
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
            'name' => 'nullable|string|max:255',
            'status' => 'required|boolean',
            'end_date' => 'nullable|date',
            'products' => 'nullable|array',
            'products.*.product_id' => 'required|exists:products,id',
            'products.*.discount_amount' => 'required|numeric|min:0',
            'products.*.discount_type' => 'required|in:percentage,fixed',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please specify the flash sale status.',
            'products.*.product_id.required' => 'Please select a valid product.',
            'products.*.product_id.exists' => 'One of the selected products is invalid.',
            'products.*.discount_amount.required' => 'Discount amount is required for all flash sale products.',
            'products.*.discount_type.required' => 'Discount type must be selected for all products.',
        ];
    }
}
