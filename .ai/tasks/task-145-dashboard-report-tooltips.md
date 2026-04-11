# Task-145: Dashboard & Report Tooltips

## Objective
Enhance the Admin UX by adding informative tooltips to all metric cards and report table headers, explaining exactly what each metric represents and how it is calculated.

## Implementation Steps

### 1. Main Dashboard (`resources/views/admin/dashboard.blade.php`)
- **Revenue Cards:** Add tooltips explaining that only 'Delivered' orders are counted.
- **Profit Cards:** Add tooltips explaining the calculation (Revenue - Procurement Cost).
- **Charts:** Add tooltips to the chart card headers.
- **Summary Cards:** Tooltips for Total Products, Customers, Orders, and Pending status.

### 2. Sales Reports (`resources/views/admin/reports/sales.blade.php`)
- **Net Sales:** Tooltip for "Total amount received after all discounts".
- **Gross Profit:** Tooltip for "Net Sales minus Total Procurement Cost".
- **AOV:** Tooltip for "Net Sales divided by total number of orders".

### 3. Inventory Valuation (`resources/views/admin/reports/inventory.blade.php`)
- **Total Valuation:** Tooltip for "Total value of on-hand stock based on batch unit costs".
- **Units In-Stock:** Tooltip for "Live physical quantity across all saleable batches".

### 4. Stock Reports (`resources/views/admin/reports/stock.blade.php`)
- **In-Stock/Damaged:** Breakdown explanations.
- **Valuation:** Calculation details.
- **Table Headers:** Tooltips for Movement types, Aging categories, etc.

### 5. Warehouse Performance (`resources/views/admin/reports/warehouse-performance/index.blade.php` & `show.blade.php`)
- **Fill Rates:** Formula tooltips (shipped vs initial demand).
- **Wastage Rate:** Formula tooltips (internal damage vs total inflows).
- **Stock Turnover:** COGS / Avg Inventory formula.

## Verification
- Hover over all modified elements to ensure tooltips appear and are legible.
- Verify consistency in tone and terminology across all pages.
- Run `php artisan optimize`.
