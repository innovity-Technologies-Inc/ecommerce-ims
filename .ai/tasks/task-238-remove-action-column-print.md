# Task 238: Remove Action Column from HRM Print Views

Ensure the "Action" column is excluded from HRM printed reports.

## 1. Requirement Details
- **Requirement ID:** REQ-238
- **Focus:** UI/UX Consistency & Reporting.
- **Description:** When printing reports in the HRM module (Payslip Index, Payslip Details), the "Action" column remains visible but empty or cluttered with button containers. It should be entirely removed from the print layout.

## 2. Implementation Steps
1. Modify `resources/views/admin/hrm/payslip/index.blade.php`:
    - Update the JavaScript print logic to identify and remove the "Action" column cells.
2. Modify `resources/views/admin/hrm/payslip/show.blade.php`:
    - Update the JavaScript print logic to identify and remove the "Action" column cells.
3. Verify `resources/views/admin/hrm/attendance/index.blade.php` to ensure it doesn't have an action column in its print view.

## 3. Verification Criteria
- [x] Verify "Action" column is missing from the print preview of Payslip Management.
- [x] Verify "Action" column is missing from the print preview of Payslip Batch Details.
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` under Section 3.12 (HRM Module) to mention print layout optimizations.
Applied fuzzy match at line 17.
