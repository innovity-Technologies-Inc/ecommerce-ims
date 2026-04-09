<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class DamageEntryRequest extends FormRequest
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
            'batch_id' => 'required|exists:batches,id',
            'product_id' => 'required|exists:products,id',
            'product_variant_id' => 'nullable|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:255',
            'serial_ids' => 'nullable|array',
            'serial_ids.*' => 'exists:batch_serials,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('quantity')) {
            $this->merge([
                'quantity' => (int) $this->quantity,
            ]);
        }
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'warehouse_id.required' => 'Please select a warehouse.',
            'batch_id.required' => 'Please select a batch.',
            'product_id.required' => 'Please select a product.',
            'quantity.min' => 'Damage quantity must be at least 1.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'warehouse_id' => 'warehouse',
            'batch_id' => 'batch',
            'product_id' => 'product',
            'quantity' => 'quantity',
        ];
    }
}
