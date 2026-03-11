# Task 18: Dynamic App Configuration Integration

Implementation of boot-level dynamic application configuration loading from database-backed general settings.

## 1. Requirement Logging
- [x] **REQ-34:** Dynamic App Configuration (App Name and Mail From Name loaded from General Settings).

## 2. Implementation Steps

### Phase 1: Service Provider Integration
- [x] Update `AppServiceProvider` boot method to check for `general_settings` table.
- [x] Synchronize `config(['app.name'])` with `GeneralSetting::business_name`.
- [x] Synchronize default `config(['mail.from.name'])` with `GeneralSetting::business_name`.
- [x] Ensure `MailSetting` continues to override specific SMTP details correctly.

### Phase 2: Verification
- [x] Verify using Tinker that `config('app.name')` correctly reflects database values.
- [x] Verify email headers and application titles use the dynamic values.

## 3. Verification Criteria
- [x] `config('app.name')` returns value from `general_settings` table.
- [x] `config('mail.from.name')` defaults to `general_settings->business_name`.
- [x] The system remains stable even if the `general_settings` table is not yet migrated.
