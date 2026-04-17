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
            'primary_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp,avif', 'max:2048'],
            'gallery_images' => ['nullable', 'array', 'max:5'],
            'gallery_images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp,avif', 'max:2048'],
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $input = $this->all();

        if (isset($input['discount_percentage'])) {
            $input['discount_percentage'] = (int) $input['discount_percentage'];
        }
        if (isset($input['min_stock_global'])) {
            $input['min_stock_global'] = (int) $input['min_stock_global'];
        }
        if (isset($input['warehouse_limits']) && is_array($input['warehouse_limits'])) {
            foreach ($input['warehouse_limits'] as $key => $val) {
                $input['warehouse_limits'][$key] = (int) $val;
            }
        }

        if (isset($input['variants']) && is_array($input['variants'])) {
            foreach ($input['variants'] as $key => $variant) {
                if (isset($variant['discount_percentage'])) {
                    $input['variants'][$key]['discount_percentage'] = (int) $variant['discount_percentage'];
                }
                if (isset($variant['min_stock_global'])) {
                    $input['variants'][$key]['min_stock_global'] = (int) $variant['min_stock_global'];
                }
                if (isset($variant['warehouse_limits']) && is_array($variant['warehouse_limits'])) {
                    foreach ($variant['warehouse_limits'] as $wKey => $wVal) {
                        $input['variants'][$key]['warehouse_limits'][$wKey] = (int) $wVal;
                    }
                }
            }
        }

        $this->merge($input);
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
            'primary_image.mimes' => 'Allowed primary image formats are: jpeg, png, jpg, gif, svg, webp, avif.',
            'gallery_images.*.mimes' => 'Allowed gallery image formats are: jpeg, png, jpg, gif, svg, webp, avif.',
            'primary_image.max' => 'The primary image size exceeds 2MB. Please compress and try again.',
            'gallery_images.max' => 'You can upload a maximum of 5 gallery images at a time.',
            'gallery_images.*.image' => 'One or more gallery images are invalid.',
            'gallery_images.*.max' => 'One or more gallery images exceed the 2MB size limit. Please compress and try again.',
        ];
    }

    public function attributes(): array
    {
        return [
            'primary_image' => 'Primary Image',
            'gallery_images' => 'Gallery Images',
            'gallery_images.*' => 'Gallery Image',
        ];
    }
}
