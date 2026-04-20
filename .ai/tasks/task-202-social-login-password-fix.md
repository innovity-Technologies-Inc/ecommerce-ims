# Task 202: Social Login Password Fix & Profile Refactoring

Allow users registered via Google/Social login to set/update their password without requiring a "Current Password" if one hasn't been set. Refactor the customer profile logic to use Service Layer and Form Requests.

## Requirement Reference
- **REQ-202:** Social Login Password Fix.

## Implementation Steps

### 1. Form Requests Creation/Update
- **File:** `app/Http/Requests/Client/ProfileUpdateRequest.php` (Update)
    - Add `mobile` field to rules.
- **File:** `app/Http/Requests/Client/UpdatePasswordRequest.php` (New)
    - Logic: `current_password` is required ONLY if `auth()->user()->password` is NOT null.
- **File:** `app/Http/Requests/Client/UpdateAddressRequest.php` (New)
    - Validation for address, city, state, country, zip.

### 2. Service Layer Implementation
- **File:** `app/Services/CustomerProfileService.php` (New)
    - `updateProfile(int $userId, array $data): bool`
    - `updatePassword(int $userId, string $newPassword, ?string $currentPassword = null): array` (returns status and message/errors)
    - `updateAddress(int $userId, array $data): bool`

### 3. Controller Refactoring
- **File:** `app/Http/Controllers/CustomerController.php`
    - Inject `CustomerProfileService`.
    - Refactor `profileUpdate`, `changePassword`, and `addressUpdate` to use the service and form requests.

### 4. UI Update
- **File:** `resources/views/client/auth/account_info.blade.php`
    - Remove `@if(!$user->google_id)` check around the password section.
    - Conditionally show the "Current Password" field only if `$user->password` is set.

### 5. Verification
- **Test Case 1:** Regular user changes password (requires current password).
- **Test Case 2:** Google Auth user sets password for the first time (no current password field shown/required).
- **Test Case 3:** Google Auth user updates their already set password (requires current password).

## Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to reflect the service layer for customer profile management.
