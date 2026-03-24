<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class BrandRequest extends FormRequest
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
            'name' => ['required', 'string', 'max:255'],
            'icon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'status' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please provide a brand name.',
            'name.max' => 'The brand name should not exceed 255 characters.',
            'icon.image' => 'The file uploaded must be an image.',
            'icon.mimes' => 'The icon must be a file of type: png, jpg, jpeg, svg, webp.',
            'icon.max' => 'The icon size should not exceed 2MB.',
        ];
    }
}
