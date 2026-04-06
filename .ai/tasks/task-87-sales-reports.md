# Task 87: Sales Reporting Module

Implement a comprehensive sales reporting dashboard in the Admin Panel to track key performance metrics and analyze sales data with granular filtering and grouping.

## Requirements

### Metrics to Calculate
- **Gross Sales:** Total sales value before any discounts.
- **Net Sales:** Total sales value after product and coupon discounts.
- **Orders Count:** Total number of orders placed.
- **Units Sold:** Total number of individual items sold.
- **Average Order Value (AOV):** Net Sales / Orders Count.
- **Discount Amount:** Total value of discounts (Product + Coupon).
- **Shipping Revenue:** Total shipping charges collected.
- **Cost (COGS):** Total procurement cost of sold items (from `total_cost` fields).
- **Gross Profit:** Net Sales - Cost.
- **Gross Margin %:** (Gross Profit / Net Sales) * 100.
- **Entity Sales:** Sales breakdowns by Product, Variant, Warehouse, Batch, and Payment Method.

### Filters & Grouping
- **Time Grouping:** Daily, Weekly, Monthly, Yearly.
- **Date Range:** Custom start and end dates.
- **Entity Filters:** Warehouse, Product, Variant, Category, Brand, Payment Method, Payment Status, Order Status.

## Implementation Plan

### 1. Service Layer (`app/Services/ReportService.php`)
- Create a `ReportService` class.
- Implement `getSalesSummary(array $filters)`:
    - Use `FlexSearch` logic to apply filters to the `Order` and `OrderItem` queries.
    - Calculate aggregate metrics using raw SQL for performance.
    - Support grouping by date (daily, weekly, monthly, yearly).
- Implement `getSalesByEntity(string $entity, array $filters)`:
    - Provide breakdowns for products, variants, warehouses, and batches.
    - Join with `ordered_product_batches` for warehouse/batch-level reporting.

### 2. Controller (`app/Http/Controllers/Admin/ReportController.php`)
- Create `ReportController`.
- `index(Request $request)`: Retrieve summary data and pass to the view.
- `export(Request $request)`: (Optional/Future) Export reports to Excel/PDF.

### 3. UI Implementation (`resources/views/admin/reports/sales.blade.php`)
- **Summary Cards:** Display top-level metrics (Net Sales, Profit, AOV, etc.) with modern Bootstrap 5 styling.
- **Filters Sidebar/Top-bar:** Integrated filters for date ranges and entity types.
- **Charts (Optional):** Integrate a lightweight library like Chart.js (if permitted) or use styled progress bars/tables for breakdowns.
- **Data Tables:** Responsive tables showing the grouped data (e.g., Sales by Day).

### 4. Routing (`routes/web.php`)
- Register report routes under the `admin` prefix with proper permissions (`reports.view`).

## Verification Criteria
- [x] Sales summary matches the sum of individual orders for a given date range.
- [x] Cost and Profit calculations correctly pull from the `total_cost` fields populated during fulfillment.
- [x] Filtering by Warehouse correctly joins `ordered_product_batches`.
- [x] Grouping by Month/Year correctly aggregates data.
- [x] UI is responsive and follows the Admin theme.
- [x] Run `./vendor/bin/pint --dirty`.
- [x] Run `php artisan optimize`.
- [x] Update `PROJECT_DOCUMENTATION.md`.
