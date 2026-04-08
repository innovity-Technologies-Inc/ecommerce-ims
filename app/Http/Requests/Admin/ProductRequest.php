<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId = $this->route('product') ? $this->route('product')->id : null;

        return [
            'category_id' => ['required', 'exists:categories,id'],
            'sub_category_id' => ['nullable', 'exists:categories,id'],
            'brand_id' => ['nullable', 'exists:brands,id'],
            'name' => ['required', 'string', 'max:255'],
            'short_description' => ['nullable', 'string'],
            'description' => ['nullable', 'string'],
            'regular_price' => ['nullable', 'numeric', 'min:0'],
            'discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'is_hot_deal' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'is_top_pick' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'min_stock_global' => ['nullable', 'integer', 'min:0'],
            'min_stock_type' => ['nullable', 'in:global,warehouse'],
            'warehouse_limits' => ['nullable', 'array'],
            'warehouse_limits.*' => ['nullable', 'integer', 'min:0'],
            'variants' => ['nullable', 'array'],
            'variants.*.variant_name' => ['required_with:variants', 'string', 'max:255'],
            'variants.*.sku' => [
                'nullable',
                'string',
                function ($attribute, $value, $fail) use ($productId) {
                    if (empty($value)) {
                        return;
                    }

                    $exists = \App\Models\ProductVariant::where('sku', $value)
                        ->when($productId, function ($query) use ($productId) {
                            return $query->where('product_id', '!=', $productId);
                        })
                        ->exists();

                    if ($exists) {
                        $fail('The SKU has already been taken.');
                    }
                },
            ],
            'variants.*.regular_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.discount_percentage' => ['nullable', 'integer', 'min:0', 'max:100'],
            'variants.*.min_stock_global' => ['nullable', 'integer', 'min:0'],
            'variants.*.min_stock_type' => ['nullable', 'in:global,warehouse'],
            'variants.*.warehouse_limits' => ['nullable', 'array'],
            'variants.*.warehouse_limits.*' => ['nullable', 'integer', 'min:0'],
            'primary_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:5'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'category_id.required' => 'Please select a category for the product.',
            'category_id.exists' => 'The selected category is invalid.',
            'name.required' => 'The product name is required.',
            'regular_price.numeric' => 'Regular price must be a valid number.',
            'discount_percentage.integer' => 'Discount must be a whole number percentage.',
            'discount_percentage.max' => 'Discount percentage cannot exceed 100%.',
            'min_stock_global.integer' => 'Minimum stock must be a whole number.',
            'variants.*.variant_name.required_with' => 'Each variant must have a name.',
            'variants.*.sku.unique' => 'The SKU has already been taken by another product or variant.',
            'variants.*.min_stock_global.integer' => 'Variant minimum stock must be a whole number.',
            'primary_image.max' => 'The primary image must not be larger than 2MB.',
            'gallery_images.max' => 'You can upload a maximum of 5 gallery images at a time.',
            'gallery_images.*.image' => 'One or more files uploaded to the gallery are not valid images.',
            'gallery_images.*.max' => 'Each gallery image must be smaller than 2MB.',
        ];
    }
}
