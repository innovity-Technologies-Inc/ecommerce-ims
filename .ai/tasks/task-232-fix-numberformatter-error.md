# Task 232: Fix Missing NumberFormatter Class

Resolve the `Error: Class "NumberFormatter" not found` in `app/HelperClass.php` on staging.

## 1. Requirement Details
- **Requirement ID:** REQ-232
- **Focus:** PHP Extension Compatibility.
- **Description:** Some servers may not have the PHP `intl` extension enabled. The `numberToWords` helper should have a fallback or a pure PHP implementation to ensure the application doesn't crash.

## 2. Implementation Steps
1. Modify `app/HelperClass::numberToWords` in `app/HelperClass.php`.
2. Add a check for `class_exists('NumberFormatter')`.
3. If it doesn't exist, use a fallback logic (pure PHP implementation or simple string representation) to prevent the fatal error.
4. Log a warning when the fallback is used to notify about the missing extension.

## 3. Verification Criteria
- [x] Test `numberToWords` logic with and without `NumberFormatter` (simulated via reflection).
- [x] Ensure the HRM payslip statement page loads without error on staging (Customer verification pending).
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- Update `PROJECT_DOCUMENTATION.md` to mention the dependency on the `intl` extension or the implemented fallback.
