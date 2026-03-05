<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SliderRequest extends FormRequest
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
        $rules = [
            'title' => 'required|string|max:255',
            'subtitle' => 'nullable|string|max:255',
            'subtext' => 'nullable|string|max:255',
            'button_name' => 'nullable|string|max:255',
            'button_url' => 'nullable|url|max:255',
            'is_active' => 'nullable',
            'position' => 'nullable|integer',
        ];

        if ($this->isMethod('POST')) {
            $rules['image'] = 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        } else {
            $rules['image'] = 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048';
        }

        return $rules;
    }
}
