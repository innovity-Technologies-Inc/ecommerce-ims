# Task 57: reCAPTCHA v2 Implementation and Environment-Based Settings

Revert dynamic reCAPTCHA, Mail, and Social Login settings to be managed strictly via the `.env` file and implement reCAPTCHA v2.

## Requirements
- [x] **Data Migration:** Transfer existing database values for Mail, Social Login, and reCAPTCHA to `.env`.
- [x] **Code Cleanup:**
    - [x] Remove dynamic config overrides from `AppServiceProvider.php`.
    - [x] Delete models: `MailSetting`, `SocialLoginSetting`, `CaptchaSetting`.
    - [x] Delete migrations for the above models.
    - [x] Delete Admin views for Mail, Social Login, and Captcha settings.
- [x] **reCAPTCHA v2 Implementation:**
    - [x] Update `master.blade.php` to use `NoCaptcha::renderJs()`.
    - [x] Update `LoginRequest` and `RegisterRequest` to use `captcha` validation rule statically.
    - [x] Update `login.blade.php` and `register.blade.php` to use `NoCaptcha::display()`.
- [x] **Verification:**
    - [x] Verify reCAPTCHA v2 works on login and registration.
    - [x] Verify Mail and Social Login work using `.env` values.
    - [x] Run `php artisan optimize` and `vendor/bin/pint --dirty`.

## Implementation Steps

1. **Environment Setup:**
    - Move database values to `.env`. [DONE]
    - Update `config/services.php` to use `env()` for Google credentials. [DONE]

2. **Revert dynamic logic:**
    - Remove logic from `AppServiceProvider.php`. [DONE]
    - Remove methods from `SettingsController` and `SettingsService`. [DONE]
    - Remove sidebar links and routes. [DONE]

3. **reCAPTCHA v2 Integration:**
    - Update views and Form Requests. [DONE]

4. **Cleanup:**
    - Delete obsolete files (Models, Migrations, Requests, Views, Seeders). [DONE]
    - Drop tables from DB. [DONE]
    - `php artisan optimize`. [DONE]
    - `vendor/bin/pint --dirty`. [DONE]
