<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReturnReceiveRequest extends FormRequest
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
            'items' => 'required|array',
            'items.*.condition' => 'required|in:damage,intact',
            'items.*.allocations' => 'required|array',
            'items.*.allocations.*.batch_id' => 'required|exists:batches,id',
            'items.*.allocations.*.quantity' => 'required|integer|min:1',
            'items.*.allocations.*.batch_serial_ids' => 'nullable|array',
            'items.*.allocations.*.batch_serial_ids.*' => 'exists:batch_serials,id',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('items') && is_array($this->items)) {
            $items = $this->items;
            foreach ($items as $itemKey => $item) {
                if (isset($item['allocations']) && is_array($item['allocations'])) {
                    foreach ($item['allocations'] as $allocKey => $alloc) {
                        if (isset($alloc['quantity'])) {
                            $items[$itemKey]['allocations'][$allocKey]['quantity'] = (int) $alloc['quantity'];
                        }
                    }
                }
            }
            $this->merge(['items' => $items]);
        }
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Please set the condition for the items to be returned.',
            'items.*.condition.required' => 'Every selected item must have a condition specified.',
        ];
    }
}
