<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ReturnRequestStatusUpdateRequest extends FormRequest
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
            'status' => 'required|in:approved,rejected',
            'rejection_reason' => 'required_if:status,rejected|string|nullable',
            'items' => 'required_if:status,approved|array',
            'items.*.condition' => 'required_with:items|in:damage,intact',
            'items.*.allocations' => 'required_if:status,approved|array',
            'items.*.allocations.*.batch_id' => 'required_if:status,approved|exists:batches,id',
            'items.*.allocations.*.quantity' => 'required_if:status,approved|integer|min:1',
            'items.*.allocations.*.batch_serial_id' => 'nullable|exists:batch_serials,id',
        ];
    }

    public function messages(): array
    {
        return [
            'status.required' => 'Please select a status for the return request.',
            'rejection_reason.required_if' => 'A rejection reason is mandatory when rejecting a request.',
            'items.required_if' => 'Please set the condition for the items to be returned.',
            'items.*.condition.required_with' => 'Every selected item must have a condition specified.',
        ];
    }
}
