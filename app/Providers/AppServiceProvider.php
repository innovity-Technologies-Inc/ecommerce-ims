<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Services\HrmService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Event;
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

        // View Composer for Admin Header Notifications
        View::composer('admin.structure.partials.header', function ($view) {
            $unreadNotifications = AdminNotification::unread()->latest()->take(10)->get();
            $unreadCount = AdminNotification::unread()->count();

            $view->with([
                'unreadNotifications' => $unreadNotifications,
                'unreadCount' => $unreadCount,
            ]);
        });
    }
}
