<?php

namespace App\Http\Requests\Client;

use Illuminate\Foundation\Http\FormRequest;

class UpdateShippingRequest extends FormRequest
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
            'shipping_method_id' => 'required|exists:shipping_methods,id,status,1',
        ];
    }

    public function messages(): array
    {
        return [
            'shipping_method_id.required' => 'Please select a shipping method.',
            'shipping_method_id.exists' => 'The selected shipping method is invalid or inactive.',
        ];
    }
}
