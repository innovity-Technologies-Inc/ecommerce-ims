# Task 76: Refactor Damaged Products Report

## Requirement
REQ-113: Refactor Damaged Products report to prioritize Batch Number, remove saleable quantity, and show granular damaged serials in details.

## Implementation Details
1.  **Modified `InventoryService`**: Added `batch_number` sorting option to `getDamagedReport` by joining with the `batches` table. Set it as the default sort.
2.  **Created `admin.inventory.damaged.partials.table`**: A dedicated table partial for damaged products that:
    *   Moves "Batch No" to the first column.
    *   Removes the "Saleable Qty" column.
    *   Links to the new `damagedDetails` route.
3.  **Updated `admin.inventory.damaged.index`**: 
    *   Changed the @include to use the new dedicated partial.
    *   Added "Batch Number (A-Z)" to the sort dropdown.
4.  **Updated `InventoryReportController`**:
    *   Added `damagedDetails(int $id)` method.
    *   Updated `damaged` method to return the new partial for AJAX requests.
5.  **Updated `routes/web.php`**: Added `admin.inventory.damaged.show` route pointing to `damagedDetails`.
6.  **Created `admin.inventory.damaged.show`**: A dedicated details view for damaged products that:
    *   Displays only the Batch, Product, Warehouse, and Damaged Quantity.
    *   Specifically lists serial numbers with a `product_status` of 'damaged'.

## Verification Criteria
- [x] Damaged Products index page is now sorted by Batch Number by default.
- [x] Batch Number is the first column in the table.
- [x] Saleable Quantity is hidden on the index page.
- [x] Clicking "Details" on a damaged product redirects to the new granular damaged view.
- [x] Damaged details view only shows damaged quantities and their associated serials.
- [x] Stock Report (Saleable) remains unaffected and still shows both quantities.
- [x] Documentation updated in `PROJECT_DOCUMENTATION.md`.
