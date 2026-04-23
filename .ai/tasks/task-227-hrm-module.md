# Task: HRM Module Implementation (REQ-227)

## 1. Requirement Overview
Implement a basic Human Resource Management (HRM) module for admins/staff.
- **Time Tracking:** Checkbox in user management to enable daily logged-in time calculation.
- **Attendance:** Section for daily work hour entry (manual and automated calculation) using Clock-In and Clock-Out times.
- **Standard Hours:** Set daily work hours for each user in their profile (e.g., 8 hours).
- **Salary Settings:** Define salary type (daily, weekly, monthly) and amount in user profiles.
- **Payslips:** Generate payslips for employees based on salary breakdown and work hours.
- **Filtering:** Filter attendance and payslips by daily, weekly, monthly, and custom range.
- **Currency:** Use dynamic currency from general settings.

## 2. Implementation Steps

### A. Database Layer
1. **Admins Table Update:** Add `is_time_tracking` (bool), `salary_type` (enum), `salary_amount` (decimal), `daily_work_hours` (decimal).
2. **Attendance Table:** `admin_attendances` (`admin_id`, `date`, `clock_in`, `clock_out`, `total_minutes`, `is_manual`).
3. **Payslips Table:** `payslips` (`admin_id`, `payslip_number`, `month`, `year`, `salary_type`, `salary_amount`, `total_hours`, `net_salary`, `status`, `payment_date`).

### B. Service Layer
1. **HrmService:**
   - Handle attendance logging (capture first login as `clock_in` and update `clock_out` on logout).
   - Calculate total work minutes per day (aggregate session durations for auto-tracking).
   - Manual entry: Calculate `total_minutes` from provided `clock_in` and `clock_out` times.
   - Generate payslips based on attendance and salary settings.
   - Filter attendance and payslips using `FlexSearch`.

### C. Controller Layer
1. **HrmController:**
   - `attendanceIndex()`: List and filter attendance records.
   - `attendanceStore()`: Manually enter attendance with `clock_in` and `clock_out` times.
   - `payslipIndex()`: List and filter payslips.
   - `payslipGenerate()`: Logic to generate a new payslip.
   - `payslipShow()`: View/Print payslip.
2. **AdminController Update:**
   - Update `store` and `update` to handle HRM fields (`is_time_tracking`, `salary_type`, `salary_amount`, `daily_work_hours`).
   - Update forms to include the new fields.

### D. UI / Blade Templates
1. **Admin Management:** Add "Time Tracking", "Daily Work Hours", and Salary fields to User Create/Edit forms.
2. **Attendance Management:** New page for listing and manual entry using clock-in/out times.
3. **Payslip Management:** New page for listing and viewing/printing payslips.
4. **Sidebar:** Add "HRM" section with "Attendance" and "Payslips" links.

### E. Authentication Hooks
- Add listeners or middleware to track `clock_in` (login) and `clock_out` (logout) for admins with `is_time_tracking` enabled.

## 3. Verification Criteria
- [ ] Admins can be created/edited with all HRM fields (Time tracking, Work hours, Salary).
- [ ] Logged-in time is correctly calculated for time-tracked users.
- [ ] Manual work hour entry (Clock-In/Clock-Out) works for all users.
- [ ] Payslips are generated correctly based on filtered data.
- [ ] Filtering (Daily/Weekly/Monthly/Range) works on Attendance and Payslip lists.
- [ ] Currency symbol is dynamic from settings.
- [ ] `./vendor/bin/pint --dirty` runs successfully.
- [ ] `php artisan optimize` runs successfully.
- [ ] `PROJECT_DOCUMENTATION.md` is updated.
- [ ] All changes are committed with `REQ-227` reference.
