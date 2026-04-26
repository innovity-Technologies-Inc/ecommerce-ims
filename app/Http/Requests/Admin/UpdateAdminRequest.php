<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAdminRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $id = $this->route('admin') ?? $this->id;

        return [
            'employee_id' => ['nullable', 'string', 'max:50', 'unique:admins,employee_id,'.$id],
            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:admins,email,'.$id],
            'password' => ['nullable', 'confirmed', 'min:8'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,avif', 'max:2048'],
            'role' => ['required', 'exists:roles,name'],
            'is_time_tracking' => ['nullable', 'boolean'],
            'salary_amount' => ['nullable', 'numeric', 'min:0'],
            'daily_work_hours' => ['nullable', 'numeric', 'min:0.1', 'max:24'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'is_time_tracking' => $this->has('is_time_tracking') ? 1 : 0,
        ]);
    }

    public function messages(): array
    {
        return [
            'name.required' => 'The administrator name is required.',
            'email.required' => 'The email address is required.',
            'email.email' => 'Please provide a valid email address.',
            'email.unique' => 'This email address is already in use by another administrator.',
            'password.confirmed' => 'The password confirmation does not match.',
            'password.min' => 'The password must be at least 8 characters.',
            'role.required' => 'Please assign a role to the administrator.',
            'role.exists' => 'The selected role is invalid.',
            'image.max' => 'The Administrator Image size exceeds 2MB. Please compress and try again.',
        ];
    }

    public function attributes(): array
    {
        return [
            'image' => 'Administrator Image',
            'is_time_tracking' => 'Time Tracking',
            'salary_type' => 'Salary Type',
            'salary_amount' => 'Salary Amount',
            'daily_work_hours' => 'Daily Work Hours',
        ];
    }
}
