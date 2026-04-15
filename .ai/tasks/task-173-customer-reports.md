# Task 173: Comprehensive Customer Reports (REQ-173)

Implement a robust customer reporting and analytics module within the Admin Panel, following the established design patterns of existing reports.

## 1. Database & Service Layer

### Service Implementation (`App\Services\CustomerReportService`)
- Implement methods for:
    - **Overview Stats:** Total, New (Today/Month), Returning, Active/Inactive.
    - **Customer List:** Detailed list with aggregate order data (orders count, total spent, last order).
    - **RFM Analysis:** Calculate Recency, Frequency, and Monetary scores and segment users into VIP, Loyal, At Risk, and Lost.
    - **CLV Calculation:** Calculate Customer Lifetime Value using AOV, Frequency, and Lifespan.
    - **Purchase Behavior:** AOV per customer, Top categories by customer segments, Order frequency trends.
    - **Cohort Analysis:** Retention tracking grouped by signup month.
    - **Churn Prediction:** Identify Active, At Risk, and Churned users based on days since last order.
    - **Segmentation:** High Spenders, Frequent Buyers, One-time Buyers.
- **FlexSearch Integration:** Ensure all listing views support the mandatory FlexSearch engine for filtering and searching.

## 2. Admin Implementation

### Controller (`App\Http\Controllers\Admin\CustomerReportController`)
- Methods for:
    - `index()`: Dashboard overview with charts and summary cards.
    - `list()`: Filterable customer list.
    - `rfm()`: RFM analysis view.
    - `behavior()`: Purchase behavior analytics.
    - `cohort()`: Cohort analysis visualization.
- Ensure "Thin Controller" pattern is strictly followed.

### Views (`resources/views/admin/reports/customers/`)
- **Dashboard (`index.blade.php`):** Summary cards and ApexCharts for visualizations.
- **List (`list.blade.php`):** Responsive table with FlexSearch filters.
- **RFM (`rfm.blade.php`):** Segmentation visualization and detailed tables.
- **Cohort (`cohort.blade.php`):** Heatmap-style retention table.
- **Consistency:** Use Bootstrap 5, Tooltips, and maintain the "Show X to Y of Z" pagination info.

### Features
- **Export:** Implement Excel export functionality using `Maatwebsite\Excel`.
- **Print:** Implement full-data print functionality capturing all filtered results.
- **Filtering:** Date ranges, order counts, spent amounts, and status filters.

## 3. Integration & Navigation

### Routing
- Add routes under `admin.reports` prefix in `routes/web.php`.

### Sidebar
- Add "Customer Reports" link under the "Reports" section in the Admin Sidebar.

## 4. Verification Criteria
- [ ] Overview dashboard correctly displays summary statistics.
- [ ] Customer list filters (Search, Date, Orders, Spent) work accurately via FlexSearch.
- [ ] RFM segments are calculated correctly based on order history.
- [ ] CLV reflects the accurate business formula.
- [ ] Cohort retention table displays correct data by signup month.
- [ ] Export to Excel captures all filtered data rows.
- [ ] Print functionality captures full data sets.
- [ ] Visualizations (Charts) are interactive and accurate.
- [ ] Run `./vendor/bin/pint --dirty`.
- [ ] Run `php artisan optimize`.

## 5. Documentation Update
- Update `PROJECT_DOCUMENTATION.md` with the "Customer Reports & Analytics" section.
