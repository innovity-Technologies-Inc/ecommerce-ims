# Task 74: Disable Serial Number Range Logic in PO Receipt

## Description
Modify the `PurchaseOrderService` to treat serial numbers containing hyphens (`-`) as literal serial numbers instead of ranges. The system now uses Select2 for individual tag inputs, so range expansion is no longer needed and causes issues when serial numbers naturally contain hyphens.

## Requirements
- **REQ-109:** Disable Serial Number Range Logic: When receiving Purchase Orders, serial numbers containing hyphens (`-`) should be treated as literal serials instead of ranges, as the system now uses Select2 for individual tag inputs.

## Implementation Steps
1. **Modify `PurchaseOrderService`:**
    - Edit `app/Services/PurchaseOrderService.php`.
    - Simplify `parseSerialNumbers` method to remove range processing logic.
    - Ensure it still handles both array and string inputs (comma/newline separated).

## Verification Criteria
- [x] Serial numbers with hyphens (e.g., `SN-123-ABC`) are stored as-is.
- [x] Input like `SN001-SN005` is no longer expanded to 5 separate serials (unless they are separate tags).
- [x] Multiple tags/comma-separated values still work correctly.
- [x] Run `php artisan optimize`.
