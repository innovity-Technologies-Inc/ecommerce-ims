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
            'received_date' => 'required|date',
            'batch_number' => 'required|string|max:255',
            'items' => 'required|array',
            'items.*.received_quantity' => 'required|integer|min:0',
            'items.*.damaged_quantity' => 'nullable|integer|min:0',
            'items.*.received_serials' => 'nullable|array',
            'items.*.received_serials.*' => 'string',
            'items.*.damaged_serials' => 'nullable|array',
            'items.*.damaged_serials.*' => 'string',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('items') && is_array($this->items)) {
            $items = $this->items;
            foreach ($items as $key => $item) {
                if (isset($item['received_quantity'])) {
                    $items[$key]['received_quantity'] = (int) $item['received_quantity'];
                }
                if (isset($item['damaged_quantity'])) {
                    $items[$key]['damaged_quantity'] = (int) $item['damaged_quantity'];
                }
            }
            $this->merge(['items' => $items]);
        }
    }

    /**
     * Custom error messages for validation.
     */
    public function messages(): array
    {
        return [
            'batch_number.required' => 'The batch number is required for the entire receipt.',
            'items.*.received_quantity.required' => 'The received quantity is required for all items.',
            'items.*.received_quantity.integer' => 'The received quantity must be an integer.',
            'items.*.received_quantity.min' => 'The received quantity must be at least 0.',
        ];
    }
}
