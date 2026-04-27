# Task 233: Fix Recency Decimal Precision

Limit the Recency value display in the RFM Analysis report to 2 decimal places.

## 1. Requirement Details
- **Requirement ID:** REQ-233
- **Focus:** UI/UX Data Formatting.
- **Description:** The "Recency" column in the RFM Analysis table currently displays values with excessive decimal precision (e.g., 4.9316766574769 Days). It should be formatted to show exactly 2 digits after the decimal point for better readability.

## 2. Implementation Steps
1. Modify `resources/views/admin/reports/customers/rfm.blade.php`.
2. Locate the line displaying `{{ $stat['recency'] }} Days`.
3. Wrap `$stat['recency']` in `number_format($stat['recency'], 2)`.

## 3. Verification Criteria
- [x] Access the RFM Analysis report and verify that Recency values are formatted with 2 decimal places.
- [x] Run `./vendor/bin/pint --dirty` to maintain project styling.

## 4. Documentation Update
- [x] Update `PROJECT_DOCUMENTATION.md` to mention the formatting update in the Customer Reports section.
