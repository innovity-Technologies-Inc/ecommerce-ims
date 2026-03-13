<?php

namespace App\Services;

use App\Models\FlashSale;
use App\Models\FlashSaleItem;
use App\Models\Product;
use App\Models\ProductVariant;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class FlashSaleService
{
    /**
     * Get the single Flash Sale record or create one if it doesn't exist.
     */
    public function getFlashSale(): FlashSale
    {
        return FlashSale::firstOrCreate([], [
            'name' => 'Flash Sale',
            'status' => false,
        ]);
    }

    /**
     * Update Flash Sale and sync discounts.
     */
    public function updateFlashSale(array $data): FlashSale
    {
        return DB::transaction(function () use ($data) {
            $flashSale = $this->getFlashSale();

            $flashSale->update([
                'name' => $data['name'] ?? $flashSale->name,
                'status' => (bool) $data['status'],
                'end_date' => $data['end_date'] ?? null,
            ]);

            // Sync Products
            $productIds = [];
            if (isset($data['products']) && is_array($data['products'])) {
                foreach ($data['products'] as $item) {
                    $productIds[] = $item['product_id'];
                    FlashSaleItem::updateOrCreate(
                        ['flash_sale_id' => $flashSale->id, 'product_id' => $item['product_id']],
                        ['discount_amount' => $item['discount_amount'], 'discount_type' => $item['discount_type']]
                    );
                }
            }

            // Remove products not in the new list
            $removedItems = FlashSaleItem::where('flash_sale_id', $flashSale->id)
                ->whereNotIn('product_id', $productIds)
                ->get();

            foreach ($removedItems as $item) {
                $this->resetProductDiscount($item->product_id);
                $item->delete();
            }

            // Sync all discounts based on current status
            $this->syncAllDiscounts($flashSale);

            return $flashSale->load('items.product');
        });
    }

    /**
     * Sync all product and variant discounts based on Flash Sale status.
     */
    protected function syncAllDiscounts(FlashSale $flashSale): void
    {
        $items = $flashSale->items()->with('product.variants')->get();

        foreach ($items as $item) {
            if ($flashSale->status) {
                $this->applyFlashSaleDiscount($item);
            } else {
                $this->resetProductDiscount($item->product_id);
            }
        }
    }

    /**
     * Apply Flash Sale discount to a product and its variants.
     */
    protected function applyFlashSaleDiscount(FlashSaleItem $item): void
    {
        $product = $item->product;
        if (! $product) {
            return;
        }

        $discountAmount = (float) $item->discount_amount;
        $discountType = $item->discount_type;

        // Update Product
        $this->updatePricing($product, $discountAmount, $discountType, true);

        // Update Variants
        foreach ($product->variants as $variant) {
            $this->updatePricing($variant, $discountAmount, $discountType, false);
        }
    }

    /**
     * Reset discount for a product and its variants.
     */
    public function resetProductDiscount(int $productId): void
    {
        $product = Product::find($productId);
        if (! $product) {
            return;
        }

        $product->update([
            'is_flash_sale' => false,
            'discount_price' => 0,
            'discount_percentage' => 0,
        ]);

        ProductVariant::where('product_id', $productId)->update([
            'discount_price' => 0,
            'discount_percentage' => 0,
        ]);
    }

    /**
     * Helper to update pricing for Product or ProductVariant.
     */
    protected function updatePricing($model, float $discountAmount, string $discountType, bool $isProductModel): void
    {
        $regularPrice = (float) $model->regular_price;
        $discountPrice = 0;
        $discountPercentage = 0;

        if ($regularPrice > 0) {
            if ($discountType === 'percentage') {
                $discountPercentage = (int) $discountAmount;
                $discountPrice = $regularPrice - ($regularPrice * ($discountPercentage / 100));
            } else { // fixed
                $discountPrice = $regularPrice - $discountAmount;
                if ($discountPrice < 0) {
                    $discountPrice = 0;
                }
                $discountPercentage = (int) (($discountAmount / $regularPrice) * 100);
            }
        }

        $updateData = [
            'discount_price' => $discountPrice,
            'discount_percentage' => $discountPercentage,
        ];

        if ($isProductModel) {
            $updateData['is_flash_sale'] = true;
        }

        $model->update($updateData);
    }

    /**
     * Search products using FlexSearch for the selection list.
     */
    public function searchProducts(array $params = [], int $perPage = 10): LengthAwarePaginator
    {
        $query = Product::with(['primaryImage', 'category', 'brand'])->active();

        $filters = [];
        if (! empty($params['category_id'])) {
            $filters['category_id'] = $params['category_id'];
        }
        if (! empty($params['sub_category_id'])) {
            $filters['sub_category_id'] = $params['sub_category_id'];
        }
        if (! empty($params['brand_id'])) {
            $filters['brand_id'] = $params['brand_id'];
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['name', 'slug', 'category.name', 'brand.name'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        // Apply Sorting
        $sort = $params['sort'] ?? 'latest';
        switch ($sort) {
            case 'oldest':
                $query->oldest();
                break;
            case 'a-z':
                $query->orderBy('name', 'asc');
                break;
            case 'z-a':
                $query->orderBy('name', 'desc');
                break;
            case 'latest':
            default:
                $query->latest();
                break;
        }

        return $query->paginate($perPage);
    }
}
