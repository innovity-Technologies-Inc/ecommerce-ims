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

## 3. The Procurement Flow (Getting Stock)
Adding a product doesn't give it "stock". You must officially "receive" items into your warehouses.

1.  **Create Purchase Order (PO):** Go to `Inventory -> Purchase Orders`. Create a PO for the supplier and select the items you are ordering.
2.  **Receiving Stock:** When the physical items arrive at your door:
    *   Open the PO and click **"Mark as Received"**.
    *   Tell the system exactly how many arrived and if any were damaged.
    *   The system now automatically adds these items to your warehouse inventory.

---

## 4. The Order Fulfillment Flow (Selling)
When a customer places an order on your website, follow this lifecycle in the `Orders` menu:

1.  **Pending:** New orders appear here. Review the customer details.
2.  **Processing:** Move the order to "Processing" while you pick and pack the items.
3.  **Shipped (Crucial Step):**
    *   When you are ready to send the order, move it to **"Shipped"**.
    *   **Inventory Allocation:** A screen will appear asking you to pick which **Warehouse** and **Batch** the products are coming from. 
    *   *Note: Stock is only officially deducted from your records at this specific moment.*
4.  **Delivered:** Once the customer receives the package, mark it as "Delivered". This finalizes the sale in your financial reports.

---

## 5. Handling Returns (RMA)
If a customer wants to return an item, go to the `Returns` section:

1.  **Return Requests:** Review the customer's request, reason, and photos. Click "Approve" to tell the customer they can send it back.
2.  **Physical Receiving:** When the item arrives at your warehouse:
    *   Open the request and click **"Mark as Received"**.
    *   Inspect the item:
        *   **Intact:** The item is perfect and goes back into your "Saleable" stock.
        *   **Damaged:** The item is recorded as "Wastage" (Loss) and is kept out of your saleable stock.

---

## 6. Marketing & Promotions
Found under the `Promotions` section in the sidebar:

1.  **Coupons:** Create discount codes (e.g., "SAVE10"). You can set a minimum spend requirement.
2.  **Flash Sale:** Put your shop in "Flash Sale Mode" for a specific time. You can choose which products get extra discounts during this period.

---

## 7. Business Intelligence (Reports)
Use the `Reports` section to understand your business health:

1.  **Sales Reports:** See your total revenue, profit, and top-selling products. *Note: Only 'Delivered' orders are counted as revenue.*
2.  **Inventory Valuation:** See exactly how much money is "sitting on your shelves" based on what you paid for your current stock.
3.  **Stock Reports:** Identify "Stagnant" stock (items that haven't moved in 90+ days) and view a "Trace" of every single physical unit by its serial number.
4.  **Warehouse Performance:** See which warehouse is most efficient and track your "Wastage Rate" (internal losses).

---

## 8. System Settings
Go to the `Settings` section to:
*   **General Settings:** Change your Business Name, Logos, **Currency Symbol**, and the **Notification Email** for Low Stock Alerts.
*   **Contact Settings:** Update your office address, phone numbers, and social media links.

---
*For further assistance, please contact your system administrator.*
