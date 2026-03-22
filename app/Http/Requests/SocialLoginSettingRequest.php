<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SocialLoginSettingRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'google_client_id' => ['nullable', 'string', 'max:255'],
            'google_client_secret' => ['nullable', 'string', 'max:255'],
            'google_redirect_url' => ['nullable', 'string', 'max:255'],
            'google_status' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'google_client_id.max' => 'The Google Client ID should not exceed 255 characters.',
            'google_client_secret.max' => 'The Google Client Secret should not exceed 255 characters.',
        ];
    }
}
