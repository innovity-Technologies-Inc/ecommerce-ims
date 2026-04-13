# Task 169: Simplify Admin "Back" Button Text

## Requirement
REQ-169: Simplify the text of all "Back" buttons in the admin panel to just "Back", preserving icons and maintaining consistency across all views.

## Implementation Steps
- [x] **Search & Identify:** Identify all "Back to ..." buttons in `resources/views/admin`.
- [x] **Refactor Text:** Update the text to exactly "Back", preserving the `bx-arrow-back` icon.
- [x] **Handle Exceptions:** Ensure `resources/views/admin/products/show.blade.php` is updated even if it lacks an icon.
- [x] **Optimization:** Run `php artisan optimize`.
- [x] **Documentation:** Update `PROJECT_DOCUMENTATION.md` and `requirements.md` to reflect the UI simplification.

## Verification Criteria
- [x] All "Back to Dashboard", "Back to List", etc. buttons in the admin panel now say just "Back".
- [x] Icons are preserved where they were present.
- [x] `php artisan optimize` runs successfully.
- [x] `PROJECT_DOCUMENTATION.md` is updated.
