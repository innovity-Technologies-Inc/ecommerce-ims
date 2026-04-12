<?php

namespace App\Providers;

use App\Models\AdminNotification;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrapFive();

        // Share unread notifications with the admin header
        View::composer('admin.structure.partials.header', function ($view) {
            $view->with([
                'unreadNotifications' => AdminNotification::unread()->latest()->take(10)->get(),
                'unreadCount' => AdminNotification::unread()->count(),
            ]);
        });
    }
}
