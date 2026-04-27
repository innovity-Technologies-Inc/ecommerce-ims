# Task 237: Fix Auto Clock-In/Out with Existing Records

Ensure the navbar attendance button correctly stores data regardless of existing daily records.

## 1. Requirement Details
- **Requirement ID:** REQ-237
- **Focus:** Data Integrity & Business Logic.
- **Description:** The current logic skips updating the attendance record if it was marked as `is_manual` or if a record already exists for the day during clock-in. This leads to data loss if an admin uses the navbar button after a manual entry or during multiple sessions.

## 2. Implementation Steps
1. Modify `app/Services/HrmService.php`:
    - In `clockIn()`: If a record exists but has no `clock_in`, set it. Ensure `last_login_at` is always updated on the Admin model (already doing this).
    - In `clockOut()`: Remove the `! $attendance->is_manual` restriction to allow accumulation of time even if a manual record was started.
    - Ensure `clock_out` is updated to the latest time and `total_minutes` are incremented correctly.

## 3. Verification Criteria
- [x] Test clock-in/out via navbar when no record exists.
- [x] Test clock-in/out via navbar when a manual record exists for the same day.
- [x] Verify that `total_minutes` correctly accumulates multiple sessions.
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` to clarify how multiple sessions and manual/auto mixing are handled.
