<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class PurchaseOrderReceiveRequest extends FormRequest
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
            'batch_number' => 'nullable|string|max:255',
            'received_date' => 'required|date',
            'items' => 'required|array',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.damaged_quantity' => 'nullable|integer|min:0',
            'items.*.missing_quantity' => 'nullable|integer|min:0',
            'items.*.serial_numbers' => 'nullable|string',
        ];
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'items.*.received_quantity.required' => 'The received quantity is required for all items.',
            'items.*.received_quantity.integer' => 'The received quantity must be an integer.',
            'items.*.received_quantity.min' => 'The received quantity must be at least 0.',
        ];
    }
}
