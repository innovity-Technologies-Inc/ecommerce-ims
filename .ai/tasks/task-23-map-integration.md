# Task 23: Map Link Integration in Contact Settings

Add a `map_link` field to the contact settings to allow admins to manage the embedded Google Map (or other map) link.

## 1. Requirement Logging
- [x] **REQ-39:** Map Integration in Contact Settings (Add map link field to contact settings for frontend display).

## 2. Implementation Steps

### Phase 1: Database & Model
- [x] Create migration to add `map_link` to `contact_settings` table.
- [x] Update `ContactSetting` model to include `map_link` in fillable array.

### Phase 2: Request & Admin UI
- [x] Update `ContactSettingRequest` to validate `map_link`.
- [x] Add `map_link` field to the Admin Contact Settings view (`admin.settings.contact`).

### Phase 3: Verification & Documentation
- [x] Run migration.
- [x] Verify that the map link can be saved and retrieved.
- [x] Update `PROJECT_DOCUMENTATION.md`.
- [x] Run `./vendor/bin/pint --dirty`.

## 3. Verification Criteria
- [x] Admin can input and save a map link in the Contact Settings page.
- [x] The field correctly persists in the database.
- [x] The value is accessible via `App\HelperClass::contactSettings()`.
