<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StockAdjustmentRequest extends FormRequest
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
            'warehouse_id' => 'required|exists:warehouses,id',
            'batch_number' => 'required|string|max:255',
            'adjustment_date' => 'required|date',
            'remarks' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.product_variant_id' => 'nullable|exists:product_variants,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
            'items.*.serial_numbers' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Please select a target warehouse.',
            'batch_number.required' => 'A batch number is required for this adjustment.',
            'adjustment_date.required' => 'Please specify the adjustment date.',
            'items.required' => 'You must add at least one product to adjust.',
            'items.*.product_id.required' => 'Each row must have a selected product.',
            'items.*.quantity.min' => 'The quantity for each item must be at least 1.',
            'items.*.unit_cost.required' => 'Each item must have a unit cost specified.',
            'items.*.unit_cost.min' => 'Unit cost cannot be a negative value.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'warehouse_id' => 'warehouse',
            'batch_number' => 'batch number',
            'adjustment_date' => 'adjustment date',
            'items.*.product_id' => 'product',
            'items.*.quantity' => 'quantity',
            'items.*.unit_cost' => 'unit cost',
        ];
    }
}
