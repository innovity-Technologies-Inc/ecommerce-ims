# Task 235: Select2 Dropdown Height Limit

Restrict the maximum height of Select2 results to improve UX when many options are present.

## 1. Requirement Details
- **Requirement ID:** REQ-235
- **Focus:** UI/UX Consistency.
- **Description:** Select2 dropdowns currently expand to show all options, which can push the page footer down or cause excessive scrolling on pages with many options (e.g., Timezone selection). A standard `max-height` with an internal scrollbar should be applied.

## 2. Implementation Steps
1. Modify `resources/views/admin/structure/master.blade.php`:
    - Add CSS for `.select2-container--bootstrap-5 .select2-results__options`.
    - Set `max-height` to `250px` (standard height).
    - Set `overflow-y` to `auto`.

## 3. Verification Criteria
- [x] Verify that long Select2 lists (like Timezones) now have a scrollbar.
- [x] Verify that the dropdown doesn't exceed the specified maximum height.
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` under Section 4 (Design & UI) to reflect this global UI standardization.
