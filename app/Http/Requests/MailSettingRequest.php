<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MailSettingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $hasPassword = \App\Models\MailSetting::first()?->mail_password !== null;

        return [
            'mail_mailer' => ['required', 'string', 'max:255'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'string', 'max:255'],
            'mail_username' => ['required', 'string', 'max:255'],
            'mail_password' => [$hasPassword ? 'nullable' : 'required', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'mail_mailer.required' => 'Please specify the mail driver (e.g., smtp).',
            'mail_host.required' => 'The SMTP host is required.',
            'mail_port.required' => 'Please provide the SMTP port.',
            'mail_username.required' => 'The mail account username is required.',
            'mail_password.required' => 'A password is required for the mail account.',
            'mail_from_address.required' => 'Please provide a "From" email address.',
            'mail_from_address.email' => 'The "From" email address must be a valid email.',
            'mail_from_name.required' => 'Please specify a "From" name for outgoing emails.',
        ];
    }
}
