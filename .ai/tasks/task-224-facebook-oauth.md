# Task: Facebook OAuth Integration (REQ-224)

Implement "Login with Facebook" functionality using Laravel Socialite, matching the existing Google OAuth pattern.

## 1. Requirement Detail
- **What:** Allow users to register and log in using their Facebook accounts.
- **How:** 
    - Add `facebook_id` and `facebook_token` to the `users` table.
    - Configure Facebook credentials in `config/services.php`.
    - Implement redirect and callback logic in `SocialLoginController.php`.
    - Add frontend buttons for Facebook login.
- **Data:** Update `users` table and `User` model.

## 2. Implementation Steps

### Step 1: Database Schema Update
- Create a migration to add `facebook_id` and `facebook_token` to the `users` table.
- Update the `User` model's `$fillable` array.

### Step 2: Configuration
- Add `facebook` entry to `config/services.php` using environment variables.

### Step 3: Controller Implementation
- Update `app/Http/Controllers/Auth/SocialLoginController.php`:
    - Add `redirectToFacebook()` method.
    - Add `handleFacebookCallback()` method.
    - Refactor common logic between Google and Facebook if necessary to maintain clean code.

### Step 4: Routing
- Add routes in `routes/web.php` for Facebook redirect and callback.

### Step 5: UI Update
- Add "Login with Facebook" button to login and registration pages.
- Ensure consistent styling with the "Login with Google" button.

### Step 6: Verification
- Verify redirection to Facebook login page.
- Verify user creation/login upon successful callback.
- Run `php artisan optimize`.

## 3. Verification Criteria
- [x] Users can successfully log in via Facebook.
- [x] New users are created correctly with `facebook_id`.
- [x] Existing users can link their Facebook if the email matches.
- [x] UI buttons are correctly displayed and functional.
