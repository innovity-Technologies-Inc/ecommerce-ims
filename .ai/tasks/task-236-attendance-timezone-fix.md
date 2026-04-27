# Task 236: Attendance Timezone Alignment

Ensure all attendance-related operations use the dynamic business timezone from settings.

## 1. Requirement Details
- **Requirement ID:** REQ-236
- **Focus:** Data Accuracy & Business Logic.
- **Description:** Manual clock-in/out and automatic session tracking should strictly adhere to the business timezone defined in General Settings. This prevents discrepancies between server time (UTC) and local business hours.

## 2. Implementation Steps
1. Modify `app/Services/HrmService.php`:
    - Inject the business timezone when parsing manual `clock_in` and `clock_out` times.
    - Ensure `now()` calls and manual parsing use `Carbon::now($timezone)` where applicable or rely on the correctly set application timezone.
    - Verify that `storeManualAttendance` correctly calculates `totalMinutes` using the business timezone.

## 3. Verification Criteria
- [x] Verified that `storeManualAttendance` uses the dynamic business timezone for parsing.
- [x] Verified that `clockIn` and `clockOut` use `Carbon::now($timezone)` to ensure accuracy.
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` under the HRM module section to highlight timezone synchronization.
