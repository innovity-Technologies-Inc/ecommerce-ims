<?php

namespace Tests\Feature\Admin;

use App\Models\Admin;
use App\Models\Batch;
use App\Models\BatchProduct;
use App\Models\BatchSerial;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SupplierRmaTest extends TestCase
{
    use RefreshDatabase;

    protected Admin $admin;

    protected Supplier $supplier;

    protected Warehouse $warehouse;

    protected Product $product;

    protected Batch $batch;

    protected function setUp(): void
    {
        parent::setUp();

        // Create Admin with Super Admin Role
        $this->admin = Admin::factory()->create();
        $role = Role::create(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $this->admin->assignRole($role);

        // Create Supplier, Warehouse, Product
        $this->supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'mobile' => '1234567890',
            'address' => 'Test Address',
        ]);

        $this->warehouse = Warehouse::create([
            'name' => 'Test Warehouse',
            'location' => 'Test Location',
            'is_quarantine' => true,
        ]);

        $this->product = Product::create([
            'name' => 'Test Product',
            'slug' => 'test-product',
            'sku' => 'TEST-SKU',
            'regular_price' => 100,
            'unit_cost' => 50,
            'stock' => 10,
        ]);

        // Create Batch with damaged items
        $this->batch = Batch::create([
            'batch_number' => 'BATCH-001',
            'supplier_id' => $this->supplier->id,
            'warehouse_id' => $this->warehouse->id,
            'total_received_qty' => 10,
            'total_saleable_qty' => 5,
            'total_damaged_qty' => 5,
        ]);

        BatchProduct::create([
            'batch_id' => $this->batch->id,
            'product_id' => $this->product->id,
            'received_qty' => 10,
            'saleable_qty' => 5,
            'damaged_qty' => 5,
        ]);

        BatchSerial::create([
            'batch_id' => $this->batch->id,
            'warehouse_id' => $this->warehouse->id,
            'product_id' => $this->product->id,
            'serial_no' => 'SN-001',
            'product_status' => 'damaged',
            'stock_status' => 'in_stock',
        ]);
    }

    public function test_admin_can_view_rma_index(): void
    {
        $response = $this->actingAs($this->admin, 'admin')
            ->get(route('admin.inventory.rma.index'));

        $response->assertStatus(200);
    }

    public function test_admin_can_create_supplier_rma(): void
    {
        $data = [
            'supplier_id' => $this->supplier->id,
            'items' => [
                [
                    'batch_id' => $this->batch->id,
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                ],
            ],
            'remarks' => 'Returning damaged items.',
            'notify_supplier' => 1,
        ];

        $response = $this->actingAs($this->admin, 'admin')
            ->post(route('admin.inventory.rma.store'), $data);

        $response->assertRedirect(route('admin.inventory.rma.index'));
        $this->assertDatabaseHas('supplier_rmas', [
            'supplier_id' => $this->supplier->id,
            'status' => 'pending',
        ]);
        $this->assertDatabaseHas('rma_items', [
            'product_id' => $this->product->id,
            'quantity' => 2,
        ]);
    }

    public function test_admin_can_close_rma_and_update_inventory(): void
    {
        // 1. Create RMA
        $rma = \App\Models\SupplierRma::create([
            'rma_number' => 'SRMA-TEST-001',
            'supplier_id' => $this->supplier->id,
            'status' => 'shipped',
        ]);

        $item = \App\Models\RmaItem::create([
            'supplier_rma_id' => $rma->id,
            'batch_id' => $this->batch->id,
            'product_id' => $this->product->id,
            'quantity' => 1,
            'batch_serial_id' => BatchSerial::first()->id,
        ]);

        // 2. Update Status to Closed
        $response = $this->actingAs($this->admin, 'admin')
            ->put(route('admin.inventory.rma.update-status', $rma->id), [
                'status' => 'closed',
            ]);

        $response->assertRedirect();

        // 3. Verify Batch Updates
        $this->assertEquals(0, $this->batch->fresh()->total_damaged_qty);
        $this->assertEquals(0, BatchProduct::where('batch_id', $this->batch->id)->first()->damaged_qty);

        // 4. Verify Serial Updates
        $this->assertDatabaseHas('batch_serials', [
            'id' => $item->batch_serial_id,
            'product_status' => 'damaged_return',
            'stock_status' => 'returned',
        ]);

        // 5. Verify Ledger
        $this->assertDatabaseHas('stock_ledgers', [
            'transaction_type' => 'RTV_DISPATCH',
            'reason_code' => 'SUPPLIER_RMA',
            'change_qty' => -1,
        ]);
    }
}
