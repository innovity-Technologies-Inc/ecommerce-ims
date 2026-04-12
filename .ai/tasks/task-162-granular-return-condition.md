# Task-162: Granular Return Condition Logic

## Objective
Refactor the return allocation UI and logic to allow setting the condition (Intact/Damaged) per individual item split/allocation rather than once per product.

## Implementation Steps
- [x] Update `resources/views/admin/returns/show_request.blade.php`.
- [x] Remove the product-level condition dropdown.
- [x] Add a condition dropdown to each individual allocation row in the JavaScript `addReturnAllocationRow` function.
- [x] Update the grid layout to accommodate the new field.
- [x] Update `app/Http/Requests/Admin/ReturnReceiveRequest.php` to validate `condition` inside the `allocations` array.
- [x] Update `app/Services/ReturnService.php` -> `receiveReturn()` to pull the condition from each specific allocation during the `ReturnItem` split/update loop.
- [x] Run `php artisan optimize`.

## Verification
- [x] Approve a return request.
- [x] During physical receiving, add multiple splits for a single product.
- [x] Set one split to "Intact" and another to "Damage".
- [x] Process the receipt and verify that:
    - Intact units are added to saleable stock.
    - Damage units are added to wastages and marked as damaged in serials.
    - Two separate `ReturnItem` records are correctly handled in the backend.
