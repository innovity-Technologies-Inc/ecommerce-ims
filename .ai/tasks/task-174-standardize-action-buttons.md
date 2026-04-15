# Task 174: Standardize Admin Action Buttons (REQ-174)

Standardize all primary action buttons ("View", "Details", "Edit", "Create", "Delete") across the Admin Panel to display only icons (no text) for consistency and a cleaner UI.

## 1. Requirement Logging
- [x] Log REQ-174 in `.ai/requirements/requirements.md`.
- [x] Update `.ai/guidelines/design-guidelines.md` with the new button standards.

## 2. Implementation Steps

### Step 1: Standardize "View" and "Details" Buttons
- [x] Identify all "View" or "Details" buttons in table partials and report indexes.
- [x] Remove text labels.
- [x] Ensure the `solar:eye-broken` or `solar:eye-bold-duotone` icon is used.
- [x] Targets updated: Warehouse Performance, FAQs, Returns, RMAs, Adjustments, etc.

### Step 2: Standardize "Edit" Buttons
- [x] Ensure all "Edit" buttons use the `solar:pen-2-broken` or `solar:pen-new-square-bold-duotone` icon.
- [x] Remove any remaining "Edit" text labels in tables and show pages.

### Step 3: Standardize "Delete" Buttons
- [x] Ensure all "Delete" buttons use the `solar:trash-bin-trash-broken` icon.
- [x] Ensure they all have the mandatory `confirmDelete` class.

### Exceptions (Keep Text + Icons)
- [x] **Create / Add New Buttons:** Buttons like "Add New Product" or "Create Category" will keep their text for prominence.
- [x] **View All Notification:** The link in the notification dropdown remains as is.
- [x] **Back Buttons:** All "Back" buttons remain as standardized in REQ-169.

## 3. Verification Criteria
- [x] No "View", "Edit", "Details", or "Delete" buttons in listing tables contain text.
- [x] Icons are consistent across all modules (eye for view, pen for edit, etc.).
- [x] Tooltips are present on icon-only buttons to provide context on hover.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` to reflect the UI standardization.
