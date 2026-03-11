<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ContactSettingRequest extends FormRequest
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
            'company_name' => 'nullable|string|max:255',
            'company_email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'map_link' => 'nullable|string',

            'facebook_url' => 'nullable|url|max:255',
            'facebook_status' => 'nullable|boolean',

            'instagram_url' => 'nullable|url|max:255',
            'instagram_status' => 'nullable|boolean',

            'tiktok_url' => 'nullable|url|max:255',
            'tiktok_status' => 'nullable|boolean',

            'x_url' => 'nullable|url|max:255',
            'x_status' => 'nullable|boolean',

            'thread_url' => 'nullable|url|max:255',
            'thread_status' => 'nullable|boolean',

            'linkedin_url' => 'nullable|url|max:255',
            'linkedin_status' => 'nullable|boolean',

            'whatsapp_url' => 'nullable|url|max:255',
            'whatsapp_status' => 'nullable|boolean',

            'youtube_url' => 'nullable|url|max:255',
            'youtube_status' => 'nullable|boolean',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'facebook_status' => $this->has('facebook_status'),
            'instagram_status' => $this->has('instagram_status'),
            'tiktok_status' => $this->has('tiktok_status'),
            'x_status' => $this->has('x_status'),
            'thread_status' => $this->has('thread_status'),
            'linkedin_status' => $this->has('linkedin_status'),
            'whatsapp_status' => $this->has('whatsapp_status'),
            'youtube_status' => $this->has('youtube_status'),
        ]);
    }
}
