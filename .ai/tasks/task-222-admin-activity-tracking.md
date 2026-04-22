# Task 222: Admin Activity Tracking

Add `TracksAdminActivity` trait to listed models to automatically track `created_by` and `updated_by`.

## Requirements
- REQ-222: Admin Activity Tracking

## Implementation Steps
1. Add `use App\Traits\TracksAdminActivity;` import to each model.
2. Add `use TracksAdminActivity;` inside each model class.
3. Ensure existing traits are preserved (e.g., `use HasFactory, TracksAdminActivity;`).

## Models to Update
- AdminNotification
- Batch
- BatchProduct
- BatchSerial
- Brand
- Category
- ContactMessage
- ContactSetting
- Coupon
- Faq
- FlashSale
- FlashSaleItem
- GeneralSetting
- InventoryLevel
- Order
- OrderItem
- OrderStatusLog
- OrderedProductBatch
- PolicySetting
- Product
- ProductImage
- ProductVariant
- PurchaseOrder
- PurchaseOrderItem
- ReturnImage
- ReturnItem
- ReturnRequest
- RmaItem
- SectionSetting
- ShippingMethod
- Slider
- StockAdjustment
- StockAdjustmentItem
- StockLedger
- Supplier
- SupplierRma
- Warehouse
- WarehouseStockLimit
- Wastage

## Verification Criteria
- [x] Models have the trait imported and used.
- [x] Code follows project styling guidelines (run Pint).
- [x] Documentation updated.
