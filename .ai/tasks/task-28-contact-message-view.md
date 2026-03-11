# Task 28: Contact Message Detail View

This task involves adding a separate view page for individual contact messages in the admin panel.

## 1. Requirement Log
- [x] **REQ-44:** Contact Message Detail View (Individual page for viewing a single contact message in admin panel).

## 2. Affected Files
- [x] `app/Services/ContactService.php` (Update)
- [x] `app/Http/Controllers/Admin/ContactMessageController.php` (Update)
- [x] `resources/views/admin/contact_messages/index.blade.php` (Update link)
- [x] `resources/views/admin/contact_messages/show.blade.php` (Create)
- [x] `routes/web.php` (Update)

## 3. Implementation Plan

### Step 1: Update Service Layer
- [x] Add `getMessageById(int $id): ContactMessage` to `ContactService`.
- [x] Ensure `is_read` is updated to true when a message is viewed.

### Step 2: Update Controller
- [x] Add `show(int $id): View` method to `ContactMessageController`.

### Step 3: Update Routes
- [x] Add `admin.contact_messages.show` route.

### Step 4: Create/Update Views
- [x] Create `resources/views/admin/contact_messages/show.blade.php` with Bootstrap 5 styling.
- [x] Update `resources/views/admin/contact_messages/index.blade.php` to add a "View" button/link.

## 4. Verification & Testing
- Visit the admin contact messages list.
- Click "View" on a message.
- Verify message details and "Read" status update.

## 5. Finalization
- [x] Run `vendor/bin/pint --dirty`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
