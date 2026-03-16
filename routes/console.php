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
