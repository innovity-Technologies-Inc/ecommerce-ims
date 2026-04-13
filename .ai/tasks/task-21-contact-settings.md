# Task 21: Contact Settings Module

Implement a new settings module to manage company contact information (name, email, phone, address) and integrate these details into the e-commerce invoices.

## 1. Requirement Logging
- [x] **REQ-37:** Contact Settings Module (Admin settings for company name, email, address, phone number, integrated into invoices).

## 2. Implementation Steps

### Phase 1: Database & Model
- [x] Create `ContactSetting` model and migration (`company_name`, `company_email`, `phone_number`, `address`).
- [x] Make fields fillable in the model.
- [x] Run migration.

### Phase 2: Service & Controller
- [x] Create `ContactSettingRequest` for validation.
- [x] Add `updateContactSettings` logic to `SettingsService` (updateOrCreate pattern).
- [x] Add `contactSettings` (GET) and `updateContactSettings` (POST) to `SettingsController`.
- [x] Register routes under the `admin.settings` prefix.

### Phase 3: Admin UI
- [x] Create `resources/views/admin/settings/contact.blade.php` view.
- [x] Add "Contact Settings" to the admin sidebar under the Settings dropdown.

### Phase 4: Invoice Integration
- [x] Add `contactSettings()` helper method to `App\HelperClass`.
- [x] Update `resources/views/admin/orders/invoice.blade.php` to display contact data in the "Seller" section.
- [x] Update `resources/views/client/orders/invoice-print.blade.php` to display contact data in the "Seller" section.

### Phase 5: Verification & Styling
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md` to document the new module and its flow.

## 3. Verification Criteria
- [x] Admin can navigate to Settings -> Contact Settings and save details.
- [x] Data persists in the database.
- [x] Generated invoices (Admin and Client side) dynamically fetch and display these contact details instead of hardcoded placeholders.
