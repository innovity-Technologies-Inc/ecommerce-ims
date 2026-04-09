<?php

namespace App\Http\Requests\Admin;

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

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('position')) {
            $this->merge([
                'position' => (int) $this->position,
            ]);
        }
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Please enter a title for the slider.',
            'button_url.url' => 'The button link must be a valid URL.',
            'image.required' => 'A slider image is required.',
            'image.image' => 'The uploaded file must be an image.',
            'image.mimes' => 'Supported image formats: jpeg, png, jpg, gif, svg, webp.',
            'image.max' => 'The slider image size should not exceed 2MB.',
        ];
    }
}
