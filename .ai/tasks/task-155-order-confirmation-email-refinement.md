# Task-155: Order Confirmation Email Refinement

## Objective
Update the order confirmation email to show a full financial breakdown (discounts, shipping, etc.) matching the invoice structure.

## Implementation Steps
- [x] Update `resources/views/emails/orders/confirmation.blade.php`.
- [x] Refactor the `x-mail::table` to include:
    - Item pricing based on `regular_price`.
    - Gross Subtotal row.
    - Product Discount row (conditional).
    - Coupon Discount row (conditional).
    - Shipping charge row.
    - Grand Total row.
- [x] Run `php artisan optimize`.

## Verification
- [x] Review the blade template to ensure all variables (`regular_price`, `product_discount`, `discount`, `shipping_charge`) are correctly used.
- [x] Ensure the table structure is valid markdown for the Laravel mail component.
