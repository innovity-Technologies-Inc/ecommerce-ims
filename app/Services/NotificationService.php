<?php

namespace App\Services;

use App\HelperClass;
use App\Mail\LowStockAlertMail;
use App\Models\AdminNotification;
use App\Models\InventoryLevel;
use DaiyanMozumder\LaravelFlexSearch\FlexSearch;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class NotificationService
{
    /**
     * Get all admin notifications with search and filtering.
     */
    public function getAdminNotifications(array $params = [], int $perPage = 20): LengthAwarePaginator
    {
        $query = AdminNotification::query();

        $filters = [];
        if (! empty($params['type'])) {
            $filters['type'] = $params['type'];
        }
        if (! empty($params['date_from'])) {
            $filters['created_at>='] = $params['date_from'].' 00:00:00';
        }
        if (! empty($params['date_to'])) {
            $filters['created_at<='] = $params['date_to'].' 23:59:59';
        }

        $flexSearch = app(FlexSearch::class);
        $searchTerm = $params['search'] ?? null;
        $searchableColumns = ['title', 'message'];

        $query = $flexSearch->apply($query, $filters, $searchTerm, $searchableColumns);

        return $query->latest()->paginate($perPage);
    }

    /**
     * Get all items currently in low stock across all warehouses.
     */
    public function getLowStockItems(): Collection
    {
        return DB::table('inventory_levels')
            ->join('products', 'inventory_levels.product_id', '=', 'products.id')
            ->leftJoin('product_variants', 'inventory_levels.product_variant_id', '=', 'product_variants.id')
            ->join('warehouses', 'inventory_levels.warehouse_id', '=', 'warehouses.id')
            ->leftJoin('warehouse_stock_limits', function ($join) {
                $join->on('inventory_levels.warehouse_id', '=', 'warehouse_stock_limits.warehouse_id')
                    ->on('inventory_levels.product_id', '=', 'warehouse_stock_limits.product_id')
                    ->whereRaw('COALESCE(inventory_levels.product_variant_id, 0) = COALESCE(warehouse_stock_limits.product_variant_id, 0)');
            })
            ->whereRaw('inventory_levels.current_quantity <= COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global)')
            ->select([
                'inventory_levels.id',
                'inventory_levels.current_quantity',
                'inventory_levels.last_alert_sent',
                'products.name',
                'product_variants.variant_name',
                'warehouses.name as warehouse_name',
                DB::raw('COALESCE(warehouse_stock_limits.min_stock, products.min_stock_global) as min_stock'),
            ])
            ->get()
            ->map(function ($item) {
                // Suggestion: Restock to double the min_stock or a minimum of 10 units
                $target = max($item->min_stock * 2, 10);
                $item->suggested_restock = max($target - $item->current_quantity, 0);

                return (array) $item;
            });
    }

    /**
     * Check for low stock items and send notifications if needed.
     */
    public function checkAndNotifyLowStock(): int
    {
        $settings = HelperClass::generalSettings();
        $notifyEmail = $settings->notify_email;

        if (! $notifyEmail) {
            Log::info('Low Stock Check: No notification email configured in General Settings.');

            return 0;
        }

        $allLowStockItems = $this->getLowStockItems();

        // Filter items that need an alert (last alert > 24 hours ago or never sent)
        $itemsToAlert = $allLowStockItems->filter(function ($item) {
            if (! $item['last_alert_sent']) {
                return true;
            }

            return now()->parse($item['last_alert_sent'])->addHours(24)->isPast();
        });

        if ($itemsToAlert->isEmpty()) {
            return 0;
        }

        try {
            Mail::to($notifyEmail)->send(new LowStockAlertMail($itemsToAlert));

            // Trigger Admin Notification
            try {
                $count = $itemsToAlert->count();
                \App\Models\AdminNotification::create([
                    'type' => 'low_stock',
                    'title' => 'Low Stock Alert',
                    'message' => "There are {$count} items currently below their minimum stock threshold.",
                    'url' => route('admin.inventory.stock.index'),
                ]);
            } catch (\Exception $e) {
                Log::error('Low Stock Notification trigger failed: '.$e->getMessage());
            }

            // Update last_alert_sent for these items
            InventoryLevel::whereIn('id', $itemsToAlert->pluck('id'))
                ->update(['last_alert_sent' => now()]);

            return $itemsToAlert->count();
        } catch (\Exception $e) {
            Log::error('Low Stock Notification Error: '.$e->getMessage());

            return 0;
        }
    }
}
