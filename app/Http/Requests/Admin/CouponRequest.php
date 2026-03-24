<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CouponRequest extends FormRequest
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
        $id = $this->route('coupon')?->id;

        return [
            'code' => 'required|string|max:255|unique:coupons,code,'.$id,
            'apply_for' => 'required|in:total_product_price,shipping_cost',
            'min_spend' => 'required|numeric|min:0',
            'discount_type' => 'required|in:percentage,fixed',
            'discount_amount' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|required_if:discount_type,percentage|numeric|min:0',
            'usage_limit' => 'nullable|integer|min:1',
            'active_on' => 'required|date',
            'expired_on' => 'required|date|after_or_equal:active_on',
            'status' => 'nullable|boolean',
        ];
    }

    public function messages(): array
    {
        return [
            'code.required' => 'Please provide a coupon code.',
            'code.unique' => 'This coupon code already exists.',
            'apply_for.required' => 'Please select where the coupon should be applied.',
            'min_spend.required' => 'Please specify the minimum spend required.',
            'discount_type.required' => 'Please select a discount type.',
            'discount_amount.required' => 'Please specify the discount amount.',
            'max_discount_amount.required_if' => 'Maximum discount amount is required for percentage discounts.',
            'active_on.required' => 'Please select an activation date.',
            'expired_on.required' => 'Please select an expiry date.',
            'expired_on.after_or_equal' => 'The expiry date must be after or equal to the activation date.',
        ];
    }
}
