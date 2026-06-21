# smart-ecom Business Operational Guide

Welcome to your **smart-ecom** administrative platform. This guide provides a step-by-step walkthrough of how to manage your store, inventory, and orders efficiently.

---

## 1. Setting Up Your Infrastructure (First Steps)
Before you can sell products, you need to define *where* they are kept and *who* provides them.

1.  **Warehouses:** Go to the `Inventory` section in the sidebar and select `Warehouses`. Add your physical storage locations (e.g., "Main Showroom", "West Side Warehouse").
2.  **Suppliers:** Under the `Inventory` section, select `Suppliers`. Add the companies or vendors you buy your stock from.

---

## 2. Managing Your Catalog
This is where you define what you sell. These options are found under the `General` section of your sidebar.

1.  **Category:** Set up your product categories first to organize your shop.
2.  **Brands:** Add the brands you carry.
3.  **Products:** 
    *   Add a new product with images, descriptions, and pricing.
    *   **Variants:** If a product has different sizes or colors, add them as "Variants".
    *   **Stock Thresholds:** Set a "Global Min Stock" value. The system will email you when your stock drops below this number.

---

## 3. Customizing Your Homepage
You can control what customers see on the front page using the `Homepage` sidebar menu.

1.  **Sliders:** Upload high-quality banners for the main rotating slider.
2.  **Product Sections (Bestsellers, Hot Deals, etc.):** 
    *   Select a section (e.g., "Top Picks").
    *   Choose **"Custom"** mode to manually pick products using the searchable "Product Selector".
    *   Choose **"Auto"** mode to let the system automatically show the newest or most popular items.

---

## 4. The Procurement Flow (Getting Stock)
Adding a product doesn't give it "stock". You must officially "receive" items into your warehouses.

1.  **Create Purchase Order (PO):** Go to `Inventory -> Purchase Orders`. Create a PO for the supplier and select the items you are ordering.
2.  **Receiving Stock:** When the physical items arrive at your door:
    *   Open the PO and click **"Mark as Received"**.
    *   Tell the system exactly how many arrived and if any were damaged.
    *   **Supplier Performance:** The system automatically rates the supplier for this order based on timeliness and quality (see "Reports" section for details).

---

## 5. The Order Fulfillment Flow (Selling)
When a customer places an order on your website, follow this lifecycle in the `Orders` menu:

1.  **Pending:** New orders appear here. Review the customer details.
2.  **Processing:** Move the order to "Processing" while you pick and pack the items.
3.  **Shipped (Crucial Step):**
    *   When you are ready to send the order, move it to **"Shipped"**.
    *   **Inventory Allocation:** Pick which **Warehouse** and **Batch** the products are coming from. 
    *   *Note: Stock is only officially deducted from your records at this specific moment.*
4.  **Delivered:** Once the customer receives the package, mark it as "Delivered". This finalized the sale in your financial reports.

---

## 6. Returns & RMA (Handling Goods Sent Back)
The system handles two types of returns: from your customers and back to your suppliers.

### A. Customer Returns
When a customer wants to return an item, use the `Returns` section:
1.  **Return Requests:** Review the request, reason, and proof photos. Click "Approve" to tell the customer they can ship it back.
2.  **Physical Receiving:** When the item arrives at your warehouse, open the request and click **"Mark as Received"**. 
3.  **Condition Inspection:** You must select the condition for each item received:
    *   **Intact (Restock):** Item is perfect and goes back into **Saleable Inventory** immediately.
    *   **Damaged (Wastage):** Item is marked as **Damaged**, removed from saleable stock, and logged in the **Wastages** list.

### B. Supplier RMA (Return to Vendor)
If you need to send damaged items back to your vendor, use `Inventory -> Supplier RMA`. The stock is permanently removed from your records once the RMA is closed.

---

## 7. Inventory Maintenance
1.  **Stock Adjustment:** Manually fix stock discrepancies (e.g., extra items found) at `Inventory -> Stock Adjustment`.
2.  **Wastages:** View internal losses or damaged returns at `Returns -> Wastages`.

---

## 8. Business Intelligence (Reports & Formulas)
The `Reports` section provides deep insights into your business health using the following logic:

### 8.1 Sales Reports
*   **Net Sales:** The actual money received after all discounts. 
    *   *Formula: Gross Price - Product Discount - Coupon Discount.*
*   **Gross Profit:** Total Sales minus what you paid for the items.
    *   *Formula: Net Sales - Procurement Cost.*
*   **AOV (Avg. Order Value):** Average amount a customer spends.
    *   *Formula: Total Net Sales / Total Number of Orders.*
*   **Gross Margin %:** Your profit percentage per sale.
    *   *Formula: (Gross Profit / Net Sales) x 100.*

### 8.2 Inventory Valuation
*   **Valuation:** The total value of your current stock based on the price you paid the supplier.
    *   *Formula: Current Quantity on Hand x Unit Cost.*

### 8.3 Stock Reports & Aging
*   **Batch Aging:** Tracks how long items have been sitting in your warehouse.
    *   **Fresh:** 0 - 30 days.
    *   **Aging:** 31 - 90 days.
    *   **Stagnant:** 91+ days (Flagged for promotion or review).

### 8.4 Warehouse Performance
*   **Gross Fill Rate:** Ability to fulfill demand from on-hand stock.
    *   *Formula: (Units Shipped / Initial Demand) x 100.*
*   **Wastage Rate:** Percentage of stock lost to internal damage.
    *   *Formula: (Total Wastage / Total Items Received) x 100.*
*   **Stock Turnover:** How many times you've "cycled" your stock.
    *   *Formula: Total Sales Cost / Current Inventory Value.*

---

## 9. Supplier Performance & Alerts
### 9.1 Supplier Performance Score (Max 100)
Every time you receive a Purchase Order, the system calculates a score for that vendor:
*   **Timeliness (40 points):** Awarded if the items arrive on or before the "Expected Delivery Date".
*   **Quality (60 points):** Based on the ratio of good items received vs. damaged items.
    *   *Formula: (Good Items / Total Received) x 60.*
*   **Overall Score:** The sum of Timeliness + Quality. This is averaged across all orders for each supplier.

### 9.2 Low Stock Alert System
The system automatically monitors your stock levels to prevent stockouts:
*   **When it checks:** Every day at **09:00 AM**.
*   **Threshold:** It checks if your stock is lower than your "Min Stock" setting.
*   **The Alert:** An email is sent to your **Notification Email** (see General Settings) containing a list of items to restock.
*   **Anti-Spam:** To avoid too many emails, the system will only alert you **once every 24 hours** for the same item.
*   **Suggested Restock:** The system suggests ordering enough to reach a safe buffer.
    *   *Formula: (Min Threshold x 2) - Current Stock (Minimum of 10 units).*

---

## 10. Marketing & Promotions
1.  **Coupons:** Create codes like "SAVE10". Use "Usage Limit" to control how many times it can be used (leave blank for unlimited).
2.  **Flash Sale:** Temporarily reduce prices for a set duration.

---

## 11. System Settings & Social Logins
Go to `Settings -> General Settings` to update your **Business Name**, **Authentication Banners**, **Currency Symbol**, and **Notification Email**.

### 11.1 Social Login Setup (Google & Facebook)
To allow customers to log in with their social accounts, you must provide API credentials in your system configuration (`.env` file):

**A. Google Setup:**
1.  Go to the [Google Cloud Console](https://console.cloud.google.com/).
2.  Create a project and set up "OAuth 2.0 Client IDs".
3.  Add your Authorized Redirect URI: `https://yourdomain.com/auth/google/callback`.
4.  Copy the `Client ID` and `Secret` to your settings.

**B. Facebook Setup:**
1.  Go to the [Facebook Developers Portal](https://developers.facebook.com/).
2.  Create a "Consumer" app and add the "Facebook Login" product.
3.  Go to **"Use cases"** and click **"Edit/Customize"** on the "Authentication and account creation" card.
4.  **Crucial:** Click **"Add"** next to the **"email"** permission.
5.  Under "Settings -> Basic", get your `App ID` and `App Secret`.
6.  Under "Facebook Login -> Settings", add your Valid OAuth Redirect URI: `https://yourdomain.com/auth/facebook/callback`.

*Note: The **Currency Symbol** set here will be automatically applied to all Purchase Orders, Reports, and customer invoices across the system.*

---

## 12. Stay Informed (Notifications)
The system includes a real-time alert system to ensure you never miss an important event.

1.  **The Notification Bell:** Look at the top-right of your screen. A bell icon with a red number shows you how many new events need your attention.
2.  **Automatic Updates:** The bell icon and its list update **automatically every 60 seconds** in the background. You don't need to refresh the page to see new alerts, and it will never interrupt your work or any data you are currently typing into forms.
3.  **Types of Alerts:**
    *   **New Orders:** Triggered immediately when a customer buys something.
    *   **Return Requests:** Alerts you when a customer asks to send an item back.
    *   **Contact Messages:** Notifies you when someone fills out your contact form.
    *   **Low Stock:** A daily summary of items that are running out.
3.  **Managing Alerts:**
    *   Click the bell to see a quick preview of the latest 10 notifications.
    *   Click **"View All Notifications"** to open a full history where you can search by date or type.
    *   Click **"Mark all as read"** to clear your unread count.
    *   **Pro Tip:** Clicking any notification will automatically take you to the relevant page (e.g., clicking an order alert opens that specific Order's details).

---

## 9. Customer Reports & Analytics (REQ-173)
The Customer Reports section provides deep insights into your customer base, helping you identify your most valuable users and predict future trends.

### **9.1 Overview Metrics**
*   **Total Customers:** All registered users in the system.
*   **Guest Customers:** Unique unregistered shoppers identified by their email addresses in orders.
*   **Returning Customers:** Users who have placed more than one successful order.
*   **Active Customers:** Users who have purchased within the last 90 days.
*   **Average Order Value (AOV):** `Total Sales Revenue / Number of Orders`.

### **9.2 RFM Segmentation**
Customers are grouped into segments based on three factors:
1.  **Recency:** How many days since their last order.
2.  **Frequency:** How many orders they have placed.
3.  **Monetary:** How much they have spent in total.
*   **VIP:** Recent buyers who spend heavily and frequently.
*   **At Risk:** Customers who haven't bought anything in 3-6 months.
*   **Lost:** Customers inactive for over 6 months.

### **9.3 Cohort Analysis (Retention)**
This heatmap shows how well you retain customers over time. It groups users by their registration month and tracks what percentage of that group comes back to shop in the following months. A "Deep Blue" color indicates strong retention, while "Faded Blue" shows potential churn.

### **9.4 CLV Projections (Lifetime Value)**
The system predicts the future value of a customer over a 24-month period using the following formula:
`Projected CLV = Historical Spend + (AOV × Monthly Purchase Frequency × 24)`
*   **Historical Spend:** Total amount already spent by the customer.
*   **Projected Value:** Expected future revenue based on current habits.
*   **Tiers:** Customers are tagged as **Whales** (High Value), **Medium Value**, or **Standard** based on their total projected lifetime worth.

---

## 13. HRM & Staff Management (REQ-227)
Manage your internal team's attendance and financial compensation using the `HRM` module.

### 13.1 Staff Configuration
Before tracking time, you must configure each staff member's profile:
1.  Go to `Management -> Admins -> Users`.
2.  Edit a user and locate the **HRM Settings** section.
3.  **Time Tracking:** Enable this to automatically track their daily login/logout times.
4.  **Standard Hours:** Set their expected daily work hours (e.g., 8.0).
5.  **Salary Settings:** Define their pay rate and type (Daily, Weekly, or Monthly).

### 13.2 Attendance Tracking
*   **The Attendance Button:** A "Clock In/Out" button is **always visible** at the top of every page for all staff members. Use this button to start and end your shift with a single click.
*   **Multiple Sessions & Break Support:** The system supports multiple clock-ins and clock-outs within the same day. 
    *   **Time Accumulation:** Every time you click "Clock Out", the system calculates the duration of that session and **adds** it to your total for the day.
    *   **Session Tracking:** Your "Clock-In" time will always show the time of your **first session**, while "Clock-Out" will update to show the **most recent** time you finished working.
    *   **Mixed Entry:** The button works seamlessly even if a manual record was previously entered for you, ensuring no work time is lost.
*   **Automatic:** For users with tracking enabled, the system records their first login as "Clock-In" and their final logout as "Clock-Out".
*   **Manual Entry:** Go to `HRM -> Attendance -> Record Attendance`. Use this to manually enter shifts for any staff member by specifying the date and exact Clock-In/Clock-Out times.

### 13.3 Payslips & Compensation
1.  **Bulk Generation:** Go to `HRM -> Payslips -> Generate New Batch`.
    *   **Generation Title:** Provide a name for the batch (e.g., "April 2026 Week 1").
    *   **Date Range:** Select the **Start** and **End** dates for the period you want to pay for.
2.  **How it Works:** The system will automatically scan all employee attendance records within your selected date range and calculate their net salary based on their individual hourly rates.
    *   **Calculation Scenario: Hourly Pay**
        If a staff member has an **Hourly Rate of $10**:
        *   If they work **5 hours** during the period, they will be paid **$50**.
        *   If they work **12 hours and 30 minutes** (12.5 hrs), they will be paid **$125**.
3.  **Management:** The main Payslip list now shows **Generation Batches**. Click the **Eye icon** to view the full list of all employee payslips within that specific batch. You can update individual statuses to **"Paid"** once you have processed their payments.

*Note: All salary amounts automatically use the currency symbol defined in your General Settings.*

---

## 14. Customer Engagement
### 14.1 WhatsApp Chat Widget
*   **Feature:** A floating "Chat with us" button appears on the bottom right of the website for customers.
*   **How to Enable:** Go to `Settings -> Contact Settings`.
*   **Setup:** 
    *   Enter your WhatsApp phone number (with country code, e.g., `8801700000000`) in the **WhatsApp URL/Number** field.
    *   Set the **WhatsApp Status** to "Active".
*   **Usage:** Customers can type a message directly on your site, and clicking "Send" will open their WhatsApp app to continue the conversation with you.

---

## 15. Ready-to-Use Fashion Catalog (Demo Data)
To help you get started immediately, the system comes pre-loaded with a comprehensive **Fashion Catalog**.
*   **100+ Products:** T-Shirts, Jeans, Shoes, and Accessories with full descriptions and marketing tags.
*   **300+ Images:** High-quality fashion photography is automatically assigned to every product.
*   **Full History:** Every product includes a realistic history of **Purchase Orders**, **Warehousing**, and **Stock Adjustments**.
*   **Segmented Hubs:** Data is distributed across **20 USA-based Warehouses** and linked to **15 Professional Suppliers**.

---

## 16. Docker Environment Setup & Execution (REQ-241)
For portable, isolated, and identical setups across developers (especially on **Windows 11 with WSL2 + Docker Desktop**), use the provided Docker integration.

### 16.1 First-Time Setup
1.  **Clone the project** to your local environment.
2.  Ensure you have **WSL2** installed and **Docker Desktop** configured to use the WSL2 backend.
3.  Ensure your IDE/Terminal is running inside the WSL2 Linux environment for the best file system performance.
4.  Copy your env file (if not already done; the container entrypoint will do this automatically if missed):
    ```bash
    cp .env.example .env
    ```

### 16.2 Start the Containers
Run the following command from the root of the project to build and start all required services:
```bash
docker compose up -d --build
```
This command starts:
*   `smart-ecom-db` (MySQL 8 database on port `3306`)
*   `smart-ecom-redis` (Redis cache on port `6379`)
*   `smart-ecom-app` (PHP 8.3 application backend on port `9000`)
*   `smart-ecom-web` (Nginx web server on port `80`)
*   `smart-ecom-queue` (Laravel queue worker)
*   `smart-ecom-scheduler` (Laravel schedule runner)

### 16.3 Common Commands & Operations
Run commands inside the running PHP container using `docker compose exec`:

*   **Access the application shell:**
    ```bash
    docker compose exec app bash
    ```
*   **Generate Application Security Key:**
    ```bash
    docker compose exec app php artisan key:generate
    ```
*   **Run Database Migrations:**
    ```bash
    docker compose exec app php artisan migrate
    ```
*   **Seed the Database (with 100 fashion products, images, warehouses, POs, and HRM data):**
    ```bash
    docker compose exec app php artisan db:seed
    ```
*   **Run Optimization (Clear & Cache configs):**
    ```bash
    docker compose exec app php artisan optimize
    ```
*   **Run Code Style Linter:**
    ```bash
    docker compose exec app ./vendor/bin/pint --dirty
    ```

### 16.4 Troubleshooting & Logs
*   **View real-time logs:**
    ```bash
    docker compose logs -f
    ```
*   **Stop the environment (preserving database data):**
    ```bash
    docker compose down
    ```
*   **Destroy environment (wiping database volumes):**
    ```bash
    docker compose down -v
    ```

---
*For further assistance, please contact your system administrator.*
