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

## 11. System Settings
Go to `Settings -> General Settings` to update your **Business Name**, **Currency Symbol**, and **Notification Email** for Alerts.

---
*For further assistance, please contact your system administrator.*
