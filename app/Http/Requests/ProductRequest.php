<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'description' => ['nullable', 'string'],
            'is_new_arrival' => ['nullable', 'boolean'],
            'is_hot_deal' => ['nullable', 'boolean'],
            'is_featured' => ['nullable', 'boolean'],
            'variants' => ['required', 'array', 'min:1'],
            'variants.*.size' => ['nullable', 'string', 'max:50'],
            'variants.*.color' => ['nullable', 'string', 'max:50'],
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
            'variants.*.price' => ['required', 'numeric', 'min:0'],
            'variants.*.stock' => ['nullable', 'integer', 'min:0'],
            'images' => ['nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
        ];
    }

    /**
     * More complex validation logic can be added here if needed,
     * but the unique Size+Color combination for a product is better handled
     * in the controller or via a custom rule.
     */
    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            $variants = $this->input('variants', []);
            $combinations = [];

            foreach ($variants as $index => $variant) {
                $combination = ($variant['size'] ?? '').'|'.($variant['color'] ?? '');
                if (in_array($combination, $combinations)) {
                    $validator->errors()->add("variants.$index", 'The combination of Size and Color must be unique for this product.');
                }
                $combinations[] = $combination;
            }
        });
    }
}
