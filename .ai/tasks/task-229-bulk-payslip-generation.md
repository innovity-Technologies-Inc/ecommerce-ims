# Task-229: Bulk Payslip Generation

## Requirement
Transition from single-employee payslip generation to bulk generation for all employees. 
- Index should show "Payslip Generations" (batches).
- Each generation has a title (e.g., "April Week - 1").
- Details view shows all individual payslips in that generation.

## Implementation Steps

### 1. Database & Models
- [ ] Create migration for `payslip_generations` table: `id`, `title`, `start_date`, `end_date`, `total_employees`, `total_amount`, `created_by`, `updated_by`, `timestamps`.
- [ ] Create migration to add `payslip_generation_id` to `payslips` table.
- [ ] Create `PayslipGeneration` model with relationships.
- [ ] Update `Payslip` model to include `payslip_generation_id` and relationship.

### 2. Service Layer (`HrmService`)
- [ ] Update `getAllPayslips` (now `getAllPayslipGenerations`) to query `PayslipGeneration`.
- [ ] Add `getPayslipGenerationDetails(int $id)` to get a batch with its individual payslips.
- [ ] Rewrite `generatePayslip` (now `generateBulkPayslips`) to:
    - Validate date range.
    - Loop through all active admins.
    - Calculate hours and salary for each.
    - Create individual `Payslip` records linked to a new `PayslipGeneration`.

### 3. Controller Layer (`HrmController`)
- [ ] Update `payslipIndex` to use the new generation-based service method.
- [ ] Update `payslipCreate` to handle the bulk generation form data.
- [ ] Update `payslipShow` to display the list of individual payslips within a generation.

### 4. Views
- [ ] `admin/hrm/payslip/index.blade.php`: List generations instead of individual payslips.
- [ ] `admin/hrm/payslip/create.blade.php`: Remove employee selector, add "Generation Title" field.
- [ ] `admin/hrm/payslip/show.blade.php`: New/Updated view to list all employee payslips in the batch.

### 5. Verification
- [ ] Generate a bulk payslip batch for multiple employees.
- [ ] Verify that the index shows the batch correctly.
- [ ] Verify that clicking "View" shows all employees with their calculated amounts.
- [ ] Run `php artisan optimize`.

## Verification Criteria
- [ ] Payslips are generated for all employees in one action.
- [ ] Generations are titled and tracked.
- [ ] Total employees and total amount are correct for each batch.
