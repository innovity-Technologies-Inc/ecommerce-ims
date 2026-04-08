<?php

use App\Services\FlashSaleService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

/**
 * Check for expired flash sales and reset product discounts automatically.
 */
Artisan::command('flash-sale:check-expiry', function (FlashSaleService $service) {
    $flashSale = $service->getFlashSale();

    // We call syncAllDiscounts which now uses isActive() internally
    // to decide whether to apply or reset discounts.
    $service->syncAllDiscounts($flashSale);

    $this->info('Flash sale expiry check completed and discounts synchronized.');
})->purpose('Check for expired flash sales and reset product discounts');

// Schedule the command to run every minute
Schedule::command('flash-sale:check-expiry')->everyMinute();

/**
 * Check for low stock items and send automated email alerts.
 */
Artisan::command('inventory:check-low-stock', function (\App\Services\NotificationService $service) {
    $this->info('Starting low stock monitoring...');
    $alertCount = $service->checkAndNotifyLowStock();
    $this->info("Low stock check completed. Alerts sent for {$alertCount} items.");
})->purpose('Check for low stock and notify the designated email');

// Schedule low stock check daily
Schedule::command('inventory:check-low-stock')->dailyAt('09:00');
