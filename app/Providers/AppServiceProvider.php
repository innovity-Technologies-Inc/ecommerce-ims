<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
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

        // Dynamic Application & Mail Configuration
        try {
            // 1. General Settings (Sets App Name and default Mail From Name)
            if (\Illuminate\Support\Facades\Schema::hasTable('general_settings')) {
                $generalSetting = \App\Models\GeneralSetting::first();
                if ($generalSetting && $generalSetting->business_name) {
                    config([
                        'app.name' => $generalSetting->business_name,
                        'mail.from.name' => $generalSetting->business_name, // Default to business name
                    ]);
                }
            }

            // 2. Mail Settings (Overrides Mail configuration if defined)
            if (\Illuminate\Support\Facades\Schema::hasTable('mail_settings')) {
                $mailSetting = \App\Models\MailSetting::first();
                if ($mailSetting) {
                    config([
                        'mail.mailers.smtp.host' => $mailSetting->mail_host,
                        'mail.mailers.smtp.port' => $mailSetting->mail_port,
                        'mail.mailers.smtp.encryption' => $mailSetting->mail_encryption,
                        'mail.mailers.smtp.username' => $mailSetting->mail_username,
                        'mail.mailers.smtp.password' => $mailSetting->mail_password,
                        'mail.from.address' => $mailSetting->mail_from_address,
                    ]);

                    // Only override mail.from.name if specifically set in mail_settings
                    if ($mailSetting->mail_from_name) {
                        config(['mail.from.name' => $mailSetting->mail_from_name]);
                    }
                }
            }

            // 3. Social Login Settings (Overrides Socialite configuration if defined)
            if (\Illuminate\Support\Facades\Schema::hasTable('social_login_settings')) {
                $socialSetting = \App\Models\SocialLoginSetting::first();
                if ($socialSetting) {
                    config([
                        'services.google.client_id' => $socialSetting->google_client_id,
                        'services.google.client_secret' => $socialSetting->google_client_secret,
                        'services.google.redirect' => $socialSetting->google_redirect_url,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if DB not migrated yet
        }
    }
}
