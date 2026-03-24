<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class GeneralSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'business_name' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:10'],
            'low_stock_limit' => ['nullable', 'integer', 'min:0'],
            'dark_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'light_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'breadcrumb_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'dark_logo.image' => 'The dark logo must be an image file.',
            'dark_logo.mimes' => 'Supported formats for dark logo: jpeg, png, jpg, gif, svg, webp.',
            'light_logo.image' => 'The light logo must be an image file.',
            'light_logo.mimes' => 'Supported formats for light logo: jpeg, png, jpg, gif, svg, webp.',
            'favicon.max' => 'The favicon size should not exceed 1MB.',
            'low_stock_limit.integer' => 'The low stock limit must be a number.',
        ];
    }
}
