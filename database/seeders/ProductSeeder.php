<?php

namespace Database\Seeders;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Supplier;
use App\Models\Warehouse;
use App\Services\ProductService;
use App\Services\PurchaseOrderService;
use App\Services\StockAdjustmentService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(
        ProductService $productService,
        PurchaseOrderService $poService,
        StockAdjustmentService $adjService
    ): void {
        // 0. Clean Existing Data
        $this->command->info('Cleaning existing product and stock data...');
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        \App\Models\Product::truncate();
        \App\Models\ProductVariant::truncate();
        \App\Models\ProductImage::truncate();
        \App\Models\PurchaseOrder::truncate();
        \App\Models\PurchaseOrderItem::truncate();
        \App\Models\Batch::truncate();
        \App\Models\BatchProduct::truncate();
        \App\Models\BatchSerial::truncate();
        \App\Models\InventoryLevel::truncate();
        \App\Models\StockAdjustment::truncate();
        \App\Models\StockAdjustmentItem::truncate();
        \App\Models\StockLedger::truncate();
        \App\Models\WarehouseStockLimit::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        // 0.5. Login as Admin for Services
        $admin = \App\Models\Admin::first();
        if ($admin) {
            \Illuminate\Support\Facades\Auth::guard('admin')->login($admin);
        }

        // 1. Prepare Data Pools
        $fashion = Category::where('slug', 'fashion')->first();
        if (! $fashion) {
            $fashion = Category::create(['name' => 'Fashion', 'slug' => 'fashion', 'status' => 1]);
        }

        $subCategories = Category::where('parent_id', $fashion->id)->get();
        if ($subCategories->isEmpty()) {
            $subs = ['T-Shirts', 'Jeans', 'Shoes', 'Watches', 'Dresses', 'Jackets'];
            foreach ($subs as $sub) {
                $subCategories->push(Category::create([
                    'name' => $sub,
                    'slug' => Str::slug($sub),
                    'status' => 1,
                    'parent_id' => $fashion->id,
                ]));
            }
        }

        $brands = Brand::whereIn('slug', ['nike', 'adidas', 'zara'])->get();
        if ($brands->count() < 3) {
            $brandNames = ['Nike', 'Adidas', 'Zara', 'H&M', 'Levi\'s', 'Gucci', 'Prada', 'Puma'];
            foreach ($brandNames as $bName) {
                Brand::updateOrCreate(
                    ['slug' => Str::slug($bName)],
                    ['name' => $bName, 'status' => 1]
                );
            }
            $brands = Brand::all();
        }

        $warehouses = Warehouse::all();
        $suppliers = Supplier::all();

        if ($warehouses->isEmpty() || $suppliers->isEmpty()) {
            $this->command->error('Warehouses or Suppliers not found. Run WarehouseSeeder and SupplierSeeder first.');

            return;
        }

        // 2. Prepare Image Pool (Download 10 unique fashion images to rotate)
        $this->command->info('Downloading image pool (10 fashion images)...');
        $imagePool = [];
        $tempDir = 'temp_seed_images';
        Storage::disk('local')->makeDirectory($tempDir);

        for ($i = 1; $i <= 10; $i++) {
            try {
                $response = Http::get('https://loremflickr.com/800/600/fashion?lock='.$i);
                if ($response->successful()) {
                    $fileName = 'fashion_'.$i.'.jpg';
                    Storage::disk('local')->put($tempDir.'/'.$fileName, $response->body());
                    $imagePool[] = Storage::disk('local')->path($tempDir.'/'.$fileName);
                }
            } catch (\Exception $e) {
                $this->command->warn("Failed to download image $i: ".$e->getMessage());
            }
        }

        if (empty($imagePool)) {
            $this->command->error('Failed to download any images. Seeding without images.');
        }

        // 3. Generate 100 Products
        $this->command->info('Generating 100 fashion products with stock and POs...');

        $colors = ['Black', 'White', 'Blue', 'Red', 'Navy', 'Grey'];
        $sizes = ['S', 'M', 'L', 'XL', 'XXL'];

        for ($p = 1; $p <= 100; $p++) {
            $brand = $brands->random();
            $subCat = $subCategories->random();
            $name = $brand->name.' '.$subCat->name.' '.Str::random(5);

            $productData = [
                'category_id' => $fashion->id,
                'sub_category_id' => $subCat->id,
                'brand_id' => $brand->id,
                'name' => $name,
                'short_description' => 'A stylish '.strtolower($subCat->name).' from '.$brand->name.'.',
                'description' => 'This high-quality '.strtolower($subCat->name).' is designed for comfort and style. Perfect for any occasion.',
                'regular_price' => rand(20, 200),
                'discount_percentage' => rand(0, 30),
                'is_featured' => (rand(1, 10) > 8),
                'is_new_arrival' => (rand(1, 10) > 7),
                'status' => 1,
                'min_stock_global' => 5,
                'min_stock_type' => 'global',
            ];

            // Assign 3 images from pool
            if (! empty($imagePool)) {
                $shuffledPool = $imagePool;
                shuffle($shuffledPool);
                $images = array_slice($shuffledPool, 0, 3);

                $productData['primary_image'] = $this->createUploadedFile($images[0]);
                $productData['gallery_images'] = [
                    $this->createUploadedFile($images[1]),
                    $this->createUploadedFile($images[2]),
                ];
            }

            // Create 2-3 variants
            $variants = [];
            $vCount = rand(2, 3);
            for ($v = 1; $v <= $vCount; $v++) {
                $color = $colors[array_rand($colors)];
                $size = $sizes[array_rand($sizes)];
                $variants[] = [
                    'variant_name' => "$color / $size",
                    'size' => $size,
                    'color' => $color,
                    'regular_price' => $productData['regular_price'],
                    'sku' => strtoupper(Str::slug($brand->name)).'-'.strtoupper(Str::random(6)),
                    'min_stock_global' => 2,
                ];
            }
            $productData['variants'] = $variants;

            // Store Product
            $product = $productService->storeProduct($productData);

            // 4. Create and Receive Purchase Order for this product
            $supplier = $suppliers->random();
            $warehouse = $warehouses->random();

            $poItems = [];
            foreach ($product->variants as $variant) {
                $poItems[] = [
                    'product_id' => $product->id,
                    'product_variant_id' => $variant->id,
                    'order_quantity' => rand(50, 100),
                    'unit_cost' => $product->regular_price * 0.6,
                ];
            }

            $po = $poService->storePurchaseOrder([
                'supplier_id' => $supplier->id,
                'warehouse_id' => $warehouse->id,
                'order_date' => now()->subDays(rand(1, 30))->toDateString(),
                'status' => 'Sent',
                'items' => $poItems,
            ]);

            // Receive PO
            $receiveData = [
                'received_date' => now()->toDateString(),
                'batch_number' => 'BATCH-'.strtoupper(Str::random(8)),
                'items' => [],
            ];
            foreach ($po->items as $item) {
                $receiveData['items'][$item->id] = [
                    'received_quantity' => $item->order_quantity,
                    'damaged_quantity' => 0,
                    'received_serials' => '', // Assuming not using serials for high volume clothing
                ];
            }
            $poService->receivePurchaseOrder($po, $receiveData);

            // 5. Occasionally create a Stock Adjustment
            if (rand(1, 10) > 8) {
                $adjWarehouse = $warehouses->random();
                $adjItems = [];
                foreach ($product->variants as $variant) {
                    $adjItems[] = [
                        'product_id' => $product->id,
                        'product_variant_id' => $variant->id,
                        'quantity' => rand(5, 15),
                        'unit_cost' => $product->regular_price * 0.5,
                    ];
                }
                $adjService->storeAdjustment([
                    'warehouse_id' => $adjWarehouse->id,
                    'batch_number' => 'ADJ-BATCH-'.strtoupper(Str::random(8)),
                    'adjustment_date' => now()->toDateString(),
                    'remarks' => 'Inventory count adjustment',
                    'items' => $adjItems,
                ]);
            }

            if ($p % 10 === 0) {
                $this->command->info("Seeded $p products...");
            }
        }

        // Cleanup temp images
        Storage::disk('local')->deleteDirectory($tempDir);
        $this->command->info('Database seeding completed successfully.');
    }

    /**
     * Helper to create UploadedFile from path.
     */
    private function createUploadedFile($path)
    {
        return new UploadedFile(
            $path,
            basename($path),
            'image/jpeg',
            null,
            true
        );
    }
}
