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
            'notify_email' => ['nullable', 'email', 'max:255'],
            'primary_color' => ['nullable', 'string', 'max:20'],
            'secondary_color' => ['nullable', 'string', 'max:20'],
            'dark_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'light_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'breadcrumb_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'login_banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'register_banner' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:1024'],
        ];
    }

    public function messages(): array
    {
        return [
            'dark_logo.image' => 'The Dark Logo must be an image file.',
            'dark_logo.max' => 'The Dark Logo size exceeds 2MB. Please compress and try again.',
            'light_logo.image' => 'The Light Logo must be an image file.',
            'light_logo.max' => 'The Light Logo size exceeds 2MB. Please compress and try again.',
            'breadcrumb_image.max' => 'The Breadcrumb Image size exceeds 2MB. Please compress and try again.',
            'login_banner.max' => 'The Login Banner size exceeds 2MB. Please compress and try again.',
            'register_banner.max' => 'The Register Banner size exceeds 2MB. Please compress and try again.',
            'favicon.max' => 'The Favicon size should not exceed 1MB.',
        ];
    }

    public function attributes(): array
    {
        return [
            'dark_logo' => 'Dark Logo',
            'light_logo' => 'Light Logo',
            'breadcrumb_image' => 'Breadcrumb Image',
            'login_banner' => 'Login Banner',
            'register_banner' => 'Register Banner',
            'favicon' => 'Favicon',
        ];
    }
}
