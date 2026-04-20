# Task 216: Order History "Start Shopping" Button Fix

Ensure the "Start Shopping" button in the empty order history view stays on a single line on mobile devices.

## Requirement Reference
- **REQ-216:** Order History "Start Shopping" Button Fix.

## Implementation Steps

### 1. Style Update
- **File:** `resources/views/client/account/orders.blade.php`
- **Action:** Add `white-space: nowrap` to the "Start Shopping" button style.
- **Changes:** 
    - Update the inline `style` attribute of the button to include `white-space: nowrap;`.

### 2. Verification
- **Mobile View:** Test the empty order history page on mobile screen widths (e.g., 320px, 375px) to ensure the button text remains on a single line.

## Documentation Update
- No documentation update required for this minor UI fix.
