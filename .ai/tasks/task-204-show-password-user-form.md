# Task 204: Show Password Toggle on Admin User Form

Add the "Show Password" checkbox to the Admin user creation and edit form to toggle visibility of password and confirmation fields.

## Requirement Reference
- **REQ-204:** Show Password Toggle on Admin User Form.

## Implementation Steps

### 1. View Update
- **File:** `resources/views/admin/users/forms.blade.php`
- **Action:** Add the "Show Password" checkbox after the password confirmation field.
- **Markup:**
    ```html
    <div class="col-lg-12">
        <div class="form-check mb-3">
            <input type="checkbox" class="form-check-input toggle-password-visibility" id="show-password-user">
            <label class="form-check-label" for="show-password-user">Show Passwords</label>
        </div>
    </div>
    ```

### 2. Verification
- Verify that checking the box reveals both the "Password" and "Confirm Password" fields.
- Unchecking should hide them back as dots (password type).

## Documentation Update
- No major documentation update required, covered by Task 203 standards.
