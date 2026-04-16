# Task: CLV Projections Card Tooltip Fix

Remove the redundant central card tooltip from the "Customer Segmentation" card in the CLV Projections detailed report.

## 1. Requirements Reference
- **REQ-175:** CLV Projections Card Tooltip Fix.

## 2. Implementation Steps

### UI Changes
1. **Modify `resources/views/admin/reports/customers/clv.blade.php`**:
    - Locate the card wrapping the customer segments (Whales, Medium, Standard).
    - Remove the `data-bs-toggle="tooltip"` and `title` attributes from the card element.
    - Ensure the inner tooltips for individual columns remain intact. (COMPLETED)

## 3. Verification Criteria
- [x] Access the CLV Projections report page.
- [x] Hover over the card area (not on specific segments) and verify that no tooltip appears.
- [x] Hover over "Whales", "Medium", and "Standard" segments individually and verify that their respective tooltips still appear.

## 4. Documentation
- [x] Update `PROJECT_DOCUMENTATION.md` (Noted as a minor UI fix under Customer Reports).
