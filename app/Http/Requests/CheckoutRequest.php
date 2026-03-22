<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CheckoutRequest extends FormRequest
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
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'mobile' => 'required|string|max:20',
            'address' => 'required|string',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'country' => 'nullable|string|max:255',
            'zip' => 'nullable|string|max:20',
            'payment_method' => 'required|in:COD,Online',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter your full name for the delivery.',
            'email.required' => 'An email address is required for order updates.',
            'email.email' => 'Please provide a valid email address.',
            'mobile.required' => 'A contact number is necessary for delivery coordination.',
            'address.required' => 'Please provide your detailed shipping address.',
            'city.required' => 'Please specify your city.',
            'payment_method.required' => 'Please select a payment method to proceed.',
            'payment_method.in' => 'The selected payment method is not supported.',
        ];
    }
}
