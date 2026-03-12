# Task: Admin Live Search, Filter, and Sort

Implement AJAX-based live searching, filtering, and sorting using `laravel-flexsearch` across all admin index pages.

## Implementation Steps

1.  **Architecture**: Create a reusable AJAX response pattern for index pages.
2.  **Service Layer**: Update Service classes to accept search, filter, and sort parameters.
3.  **Controller**: Update admin controllers to handle AJAX requests and return partial views or JSON.
4.  **Frontend (JS)**: Implement a generic jQuery-based handler for live search (with debounce), filter, and sort.
5.  **Views**:
    - Update admin index views to include search inputs and sorting dropdowns.
    - Wrap the table and pagination in a container that can be dynamically updated via AJAX.
6.  **FlexSearch Integration**: Utilize `FlexSearch::apply()` in the Service layer to handle multi-column searches.
7.  **Sorting Logic**: Implement "Oldest", "Latest", "A to Z", and "Z to A" sorting.

## Target Index Pages
- Admins (`admin.index`)
- Products (`admin.products.index`)
- Categories (`admin.categories.index`)
- Brands (`admin.brands.index`)
- Shipping Methods (`admin.shipping_methods.index`)
- Orders (`admin.orders.index`)
- Customers (`admin.customers.index`)
- Sliders (`admin.sliders.index`)
- Contact Messages (`admin.contact_messages.index`)

## Verification Criteria
- [x] Live search works instantly (debounced) without page refresh.
- [x] Sorting (Oldest, Latest, A-Z, Z-A) works via AJAX.
- [x] Pagination works correctly with active search/filter/sort parameters via AJAX.
- [x] FlexSearch correctly searches across multiple columns (e.g., product name, category, brand).
- [x] UI remains responsive and provides feedback (e.g., loading state) during AJAX calls.
