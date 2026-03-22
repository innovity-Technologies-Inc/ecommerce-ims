# Task 56: Google reCAPTCHA Integration

Implement Google reCAPTCHA v2 on the client-side login and registration pages to enhance security and prevent automated bot submissions.

## 1. Prerequisites & Installation
- [x] Install `anhskohbo/no-captcha` package (Laravel 12 compatible).
- [x] Publish the package configuration.
- [x] Add `RECAPTCHA_SITE_KEY` and `RECAPTCHA_SECRET_KEY` to `.env.example`.

## 2. Backend Implementation (Validation)
- [x] Update `App\Http\Requests\Auth\LoginRequest` to include reCAPTCHA validation.
- [x] Create `App\Http\Requests\Auth\RegisterRequest` to handle registration validation (refactoring from inline validation in `RegisteredUserController`).
- [x] Update `RegisteredUserController` to use the new `RegisterRequest`.

## 3. Frontend Implementation (Views)
- [x] Update `resources/views/client/auth/login.blade.php` to include the reCAPTCHA widget.
- [x] Update `resources/views/client/auth/register.blade.php` to include the reCAPTCHA widget.
- [x] Ensure the reCAPTCHA scripts are properly loaded in the master layout specifically in the head.

## 4. Finalization
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.

## 5. Verification Criteria
- [x] reCAPTCHA widget is visible on the Login page.
- [x] reCAPTCHA widget is visible on the Registration page.
- [x] Form submission fails if reCAPTCHA is not completed or is invalid.
- [x] Form submission succeeds only when reCAPTCHA is valid and other fields are correct.
- [x] Custom validation messages are displayed for reCAPTCHA errors.
