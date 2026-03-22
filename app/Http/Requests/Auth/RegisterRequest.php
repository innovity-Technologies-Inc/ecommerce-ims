<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
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
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mobile' => ['required', 'string', 'max:20'],
            'g-recaptcha-response' => ['required', 'captcha'],
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'email.required' => 'Please provide an email address.',
            'email.unique' => 'This email address is already registered.',
            'password.required' => 'Please set a password.',
            'password.confirmed' => 'The password confirmation does not match.',
            'mobile.required' => 'Please provide your mobile number.',
            'g-recaptcha-response.required' => 'Please complete the reCAPTCHA challenge.',
            'g-recaptcha-response.captcha' => 'reCAPTCHA validation failed. Please try again.',
        ];
    }
}
