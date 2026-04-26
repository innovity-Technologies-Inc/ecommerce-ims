<?php

namespace App\Providers;

use App\Models\Admin;
use App\Models\AdminNotification;
use App\Services\HrmService;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\RateLimiter;
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

        // Standard for Login/Auth
        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        // Standard for Global Web browsing
        RateLimiter::for('global', function (Request $request) {
            return Limit::perMinute(120)->by($request->ip());
        });

        // Dynamically set timezone from database settings
        try {
            $timezone = \App\Models\GeneralSetting::value('timezone');
            if ($timezone) {
                date_default_timezone_set($timezone);
                Config::set('app.timezone', $timezone);
            }
        } catch (\Exception $e) {
            // Table might not exist during migrations
        }

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
