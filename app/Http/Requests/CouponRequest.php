<?php

namespace App\Http\Requests;

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
}
