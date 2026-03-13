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
}
