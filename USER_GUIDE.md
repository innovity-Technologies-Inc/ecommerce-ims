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
    *   The system now automatically adds these items to your warehouse inventory.

---

## 5. The Order Fulfillment Flow (Selling)
When a customer places an order on your website, follow this lifecycle in the `Orders` menu:

1.  **Pending:** New orders appear here. Review the customer details.
2.  **Processing:** Move the order to "Processing" while you pick and pack the items.
3.  **Shipped (Crucial Step):**
    *   When you are ready to send the order, move it to **"Shipped"**.
    *   **Inventory Allocation:** A screen will appear asking you to pick which **Warehouse** and **Batch** the products are coming from. 
    *   *Note: Stock is only officially deducted from your records at this specific moment.*
4.  **Delivered:** Once the customer receives the package, mark it as "Delivered". This finalized the sale in your financial reports.

---

## 6. Returns & RMA (Handling Goods Sent Back)
The system handles two types of returns: from your customers and back to your suppliers.

### A. Customer Returns
When a customer wants to return an item, use the `Returns` section:
1.  **Return Requests:** Review the request, reason, and proof photos. Click "Approve" to tell the customer they can ship it back.
2.  **Physical Receiving (The Most Important Step):** When the item arrives at your warehouse, open the request and click **"Mark as Received"**. 
3.  **Condition Inspection:** You must select the condition for each item received:
    *   **Intact (Restock):** 
        *   **System Action:** The system automatically adds the item back into your **Saleable Inventory**.
        *   **Result:** The item is now available to be sold to another customer immediately.
    *   **Damaged (Wastage):**
        *   **System Action:** The system automatically marks the item as **Damaged** and adds a record to the **Wastages** table.
        *   **Result:** The item is removed from saleable stock and tracked as a financial loss. It will appear in your "Damaged Products" list.

### B. Supplier RMA (Return to Vendor)
If you need to send damaged or incorrect items back to your vendor, use the `Inventory` section:
1.  **Supplier RMA:** Create a new RMA by selecting the Supplier and the original Purchase Order.
2.  **Item Selection:** Select the specific items (and serial numbers) you are returning.
3.  **Completion:** Once the supplier receives the items, close the RMA. The stock is permanently removed from your system records.

---

## 7. Inventory Maintenance (Adjustments & Wastage)
Use these tools under the `Inventory` and `Returns` menus to keep your stock accurate.

1.  **Stock Adjustment:** If you find a discrepancy (e.g., extra items found or missing items), go to `Inventory -> Stock Adjustment`. You can manually increase or decrease stock for any batch.
2.  **Damaged Products:** View a list of all items currently marked as "Damaged" across your warehouses at `Inventory -> Damaged Products`.
3.  **Wastages:** View a history of all internal losses or customer-returned damaged goods at `Returns -> Wastages`. You can also manually record internal damage here using the "Add Damage Entry" button.

---

## 8. Marketing & Promotions
Found under the `Promotions` section in the sidebar:

1.  **Coupons:** Create discount codes (e.g., "SAVE10"). You can set a minimum spend requirement and usage limits (leave empty for unlimited use).
2.  **Flash Sale:** Put your shop in "Flash Sale Mode" for a specific time. You can choose which products get extra discounts during this period.

---

## 9. Customer Experience (The Frontend)
How your customers interact with the shop:

1.  **Browsing:** Customers can filter products by Category, Brand, or Price.
2.  **Applying Coupons:** During checkout, customers can click the **"View Coupons"** button to see all active offers. Clicking "Apply" on a valid offer automatically calculates their discount.
3.  **Order Tracking:** Customers can track their order status in real-time. If an order is "Rejected" or "Cancelled", they can see your specific **Remarks/Reason** on their tracking page.

---

## 10. Business Intelligence (Reports)
Use the `Reports` section to understand your business health:

1.  **Sales Reports:** See your total revenue, profit, and top-selling products. *Note: Only 'Delivered' orders are counted as revenue.*
2.  **Inventory Valuation:** See exactly how much money is "sitting on your shelves" based on what you paid for your current stock.
3.  **Stock Reports:** Identify "Stagnant" stock (items that haven't moved in 90+ days) and view a "Trace" of every single physical unit by its serial number.
4.  **Warehouse Performance:** See which warehouse is most efficient and track your "Wastage Rate" (internal losses).

---

## 11. System Settings
Go to the `Settings` section to:
*   **General Settings:** Change your Business Name, Logos, **Currency Symbol**, and the **Notification Email** for Low Stock Alerts.
*   **Contact Settings:** Update your office address, phone numbers, and social media links.

---
*For further assistance, please contact your system administrator.*
