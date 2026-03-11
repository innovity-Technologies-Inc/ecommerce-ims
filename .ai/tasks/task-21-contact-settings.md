# Task 21: Contact Settings Module

Implement a new settings module to manage company contact information (name, email, phone, address) and integrate these details into the e-commerce invoices.

## 1. Requirement Logging
- [x] **REQ-37:** Contact Settings Module (Admin settings for company name, email, address, phone number, integrated into invoices).

## 2. Implementation Steps

### Phase 1: Database & Model
- [ ] Create `ContactSetting` model and migration (`company_name`, `company_email`, `phone_number`, `address`).
- [ ] Make fields fillable in the model.
- [ ] Run migration.

### Phase 2: Service & Controller
- [ ] Create `ContactSettingRequest` for validation.
- [ ] Add `updateContactSettings` logic to `SettingsService` (updateOrCreate pattern).
- [ ] Add `contactSettings` (GET) and `updateContactSettings` (POST) to `SettingsController`.
- [ ] Register routes under the `admin.settings` prefix.

### Phase 3: Admin UI
- [ ] Create `resources/views/admin/settings/contact.blade.php` view.
- [ ] Add "Contact Settings" to the admin sidebar under the Settings dropdown.

### Phase 4: Invoice Integration
- [ ] Add `contactSettings()` helper method to `App\HelperClass`.
- [ ] Update `resources/views/admin/orders/invoice.blade.php` to display contact data in the "Seller" section.
- [ ] Update `resources/views/client/orders/invoice-print.blade.php` to display contact data in the "Seller" section.

### Phase 5: Verification & Styling
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Update `PROJECT_DOCUMENTATION.md` to document the new module and its flow.

## 3. Verification Criteria
- [ ] Admin can navigate to Settings -> Contact Settings and save details.
- [ ] Data persists in the database.
- [ ] Generated invoices (Admin and Client side) dynamically fetch and display these contact details instead of hardcoded placeholders.
