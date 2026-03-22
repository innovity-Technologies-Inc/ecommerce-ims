<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterRequest extends FormRequest
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
            'category' => 'nullable|array',
            'category.*' => 'integer|exists:categories,id',
            'category_nav' => 'nullable|integer|exists:categories,id',
            'brand' => 'nullable|array',
            'brand.*' => 'integer|exists:brands,id',
            'flash_sale' => 'nullable|array',
            'flash_sale.*' => 'integer|exists:flash_sales,id',
            'min_price' => 'nullable|numeric|min:0',
            'max_price' => 'nullable|numeric|min:0',
            'search' => 'nullable|string|max:255',
            'sort' => 'nullable|string|in:newness,price-low,price-high,a-z,z-a,in-stock',
        ];
    }

    public function messages(): array
    {
        return [
            'category.*.exists' => 'One or more selected categories are invalid.',
            'brand.*.exists' => 'One or more selected brands are invalid.',
            'min_price.numeric' => 'Minimum price must be a valid number.',
            'max_price.numeric' => 'Maximum price must be a valid number.',
            'sort.in' => 'The selected sorting option is invalid.',
        ];
    }
}
