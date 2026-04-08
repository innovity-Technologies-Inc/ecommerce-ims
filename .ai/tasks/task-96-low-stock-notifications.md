# Task 96: Implement Low Stock Notifications & Automation

## Requirement
Implement REQ-133: A system to monitor low stock at global and warehouse levels, display alerts in the dashboard, and send automated email notifications to a designated address.

## Implementation Steps

### 1. Database Migrations
- **General Settings:** Add `notify_email` (string, nullable).
- **Inventory Levels:** Add `last_alert_sent` (timestamp, nullable).

### 2. Model & Request Updates
- Update `GeneralSetting` model to include `notify_email`.
- Update `InventoryLevel` model to include `last_alert_sent`.
- Update `App\Http\Requests\Admin\GeneralSettingRequest` to validate `notify_email`.

### 3. Service & UI Updates (General Settings)
- Update `App\Services\SettingsService::updateGeneralSettings` to handle `notify_email`.
- Update `resources/views/admin/settings/general.blade.php` to include the "Notification Email" field.

### 4. Low Stock Logic & Mailable
- Create `App\Mail\LowStockAlertMail` to notify admins about low stock items.
- Include a list of items and "Suggested Quantity to Restock" (e.g., `(Min Stock * 2) - Current Quantity`).
- Update `App\Services\WarehousePerformanceService` or a dedicated `NotificationService` to identify low stock items.

### 5. Automated Scheduler & Route
- Create a new Artisan command `inventory:check-low-stock` to identify low stock items and send emails.
- Logic:
    - Identify items where `current_quantity <= min_stock` (warehouse) or global stock.
    - Only send if `last_alert_sent` is null or older than 24 hours (to prevent spam).
    - Update `last_alert_sent` after successful dispatch.
- Register the command in `routes/console.php` to run daily.
- Add a secure route (e.g., `/admin/inventory/check-low-stock`) to trigger the check manually via cron job URL.

### 6. Dashboard Update
- Add a "Low Stock Notifications" section to the admin dashboard.
- Display products/variants that have exceeded their low stock threshold.

## Verification Criteria
- `notify_email` can be saved in General Settings.
- `inventory:check-low-stock` command correctly identifies low stock items.
- Email is sent only to the configured address.
- `last_alert_sent` prevents duplicate emails within 24 hours.
- Email content includes suggested restock quantities.
- Low stock items appear correctly in the dashboard.

## Approval Hold
- **STOP.** Wait for user approval of this task design before implementation.
