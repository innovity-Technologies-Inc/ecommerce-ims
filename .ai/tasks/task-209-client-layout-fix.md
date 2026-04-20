# Task 209: Client Master Layout Style Cleanup

Remove conflicting Admin-specific CSS from the Client master layout to fix topbar and breadcrumb styling.

## Requirement Reference
- **REQ-209:** Client Master Layout Style Cleanup.

## Implementation Steps

### 1. Style Removal
- **File:** `resources/views/client/structure/master.blade.php`
- **Action:** Remove the large `<style>` block containing:
    - Summernote (`.note-editor`) overrides with `var(--bs-...)`.
    - Select2 (`.select2-container--bootstrap-5`) overrides with `var(--bs-...)`.
    - `.content-page` and `.container-fluid` spacing overrides.
- **Keep:** Only the `.filepond--credits` fix and any other strictly client-essential local styles.

### 2. Verification
- **Header:** Verify topbar links, colors, and layout are restored.
- **Breadcrumb:** Verify background image and text alignment are correct.
- **Components:** Ensure Select2 and Summernote still look acceptable (they should use default theme styles or be styled specifically for client if needed).

### 3. Build Refresh
- Run `php artisan optimize` to refresh view cache.

## Documentation Update
- No documentation update required for this bug fix.
