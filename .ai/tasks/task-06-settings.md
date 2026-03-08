# Task: Site Settings Management (REQ-18, REQ-19, REQ-20)

## Status: Completed [x]

## Implementation Details
1. **General Settings (REQ-18):**
   - [x] Created `App\Models\GeneralSetting`.
   - [x] Implemented identity management (Site Name, Dark/Light Logos, Favicon).
   - [x] Created `GeneralSettingRequest` for validation.
   - [x] Implemented global `HelperClass::generalSettings()` for access in views.

2. **Mail SMTP Settings (REQ-19):**
   - [x] Created `App\Models\MailSetting`.
   - [x] Implemented SMTP management (Host, Port, User, Pass, Encryption).
   - [x] Configured dynamic SMTP loading via `SettingsService`.
   - [x] Created `MailSettingRequest` for secure validation.

3. **Homepage Sections (REQ-20):**
   - [x] Created `App\Models\SectionSetting`.
   - [x] Implemented "Bestsellers" section toggle with Organic/Custom modes.
   - [x] Added visibility controls for all homepage sections.
   - [x] Implemented metadata management (Title/Subtitle) for each section.

## Verification
- [x] Confirmed that logo updates are reflected site-wide immediately.
- [x] Verified that SMTP settings are correctly saved to the DB and used for email operations.
- [x] Confirmed that the homepage correctly shows/hides sections based on admin settings.
