# Task 25: Dynamic Social Links Integration

Implement dynamic social media links management in contact settings and integrate them into the frontend (Contact page and Footer).

## 1. Requirement Logging
- [x] **REQ-41:** Dynamic Social Links (Manage social media URLs and visibility toggles in contact settings, integrated into frontend).

## 2. Implementation Steps

### Phase 1: Database & Model
- [x] Create migration to add social URL and status fields to `contact_settings` table.
    - Facebook, Instagram, Tiktok, X, Thread, Linkedin, Whatsapp, Youtube.
- [x] Update `ContactSetting` model with new fillable fields and boolean casts for statuses.

### Phase 2: Request & Admin UI
- [x] Update `ContactSettingRequest` to validate new social fields.
- [x] Update Admin Contact Settings view (`admin.settings.contact`) to include URL inputs and visibility switches for each platform.

### Phase 3: Frontend Integration
- [x] Update `resources/views/client/contact.blade.php` to render dynamic social links.
- [x] Update Footer (`resources/views/client/structure/partials/footer.blade.php`) to render dynamic social links.
- [x] Update Mobile Menu (`resources/views/client/structure/partials/header.blade.php`) if applicable.

### Phase 4: Verification & Documentation
- [x] Verify that social links can be toggled and saved.
- [x] Verify frontend icons only show when status is "On".
- [x] Update `PROJECT_DOCUMENTATION.md`.
- [x] Run `./vendor/bin/pint --dirty`.

## 3. Verification Criteria
- [x] Admin can manage 8 social platforms with specific URLs and toggles.
- [x] Icons only appear on the frontend when their respective toggle is active.
- [x] Links correctly point to the provided URLs.
