<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateOrderStatusRequest extends FormRequest
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
            'order_status' => 'required|string',
            'rejection_reason' => 'required_if:order_status,Cancelled,Rejected|nullable|string',
            'email_notify' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'order_status.required' => 'Please select an order status.',
            'rejection_reason.required_if' => 'A reason/remarks is required when the order is being Cancelled or Rejected.',
        ];
    }
}
