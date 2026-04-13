# Task 50: Registration and Checkout Refinement

Simplify the registration process and enhance the checkout experience for authenticated users by automatically updating their profiles with checkout details.

## Requirements
- **REQ-75:** Registration & Checkout Refinement.

## Implementation Steps

### 1. Database Changes
- [x] Create and run migration to make `name` nullable in the `users` table.

### 2. Registration Refinement
- [x] Update `resources/views/client/auth/register.blade.php` to include only Email, Mobile, Password, and Confirm Password fields.
- [x] Update `app/Http/Controllers/Auth/RegisteredUserController.php` to validate and store only the required fields.

### 3. Checkout Refinement
- [x] Update `app/Services/OrderService.php` in the `placeOrder` method:
    - If a user is authenticated, update their `name`, `mobile`, `address`, `city`, `state`, `country`, and `zip` in the `users` table using the data from the checkout form.
- [x] Verify `resources/views/client/checkout/index.blade.php` already fetches and populates fields from the authenticated user.

### 4. Verification
- [x] Register a new user with only Email and Mobile. Verify it works and `name` is null in the DB.
- [x] Log in and go to Checkout. Fill in the missing details (Name, Address, etc.) and place an order.
- [x] Verify the order is placed correctly with the provided data.
- [x] Verify the user's profile in the `users` table has been updated with the checkout details.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## Verification Criteria
- Registration is fast and requires only 3 inputs (Email, Mobile, Password).
- Checkout form is pre-filled for authenticated users.
- Authenticated users' profiles are automatically completed/updated upon successful order placement.
