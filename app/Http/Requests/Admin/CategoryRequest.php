<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('category') ? $this->route('category')->id : null;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                \Illuminate\Validation\Rule::unique('categories')
                    ->where('parent_id', $this->parent_id)
                    ->ignore($categoryId),
            ],
            'parent_id' => ['nullable', 'exists:categories,id'],
            'icon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp,avif', 'max:2048'],
            'status' => ['nullable'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Please enter a category name.',
            'name.max' => 'The category name should not exceed 255 characters.',
            'parent_id.exists' => 'The selected parent category is invalid.',
            'icon.image' => 'The uploaded file must be an image.',
            'icon.mimes' => 'Allowed icon formats are: png, jpg, jpeg, svg, webp, avif.',
            'icon.max' => 'The Category Icon size should not exceed 2MB. Please compress and try again.',
        ];
    }

    public function attributes(): array
    {
        return [
            'icon' => 'Category Icon',
        ];
    }
}
