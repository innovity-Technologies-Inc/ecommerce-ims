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

        // Dynamic Mail Configuration
        try {
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
                        'mail.from.name' => $mailSetting->mail_from_name,
                    ]);
                }
            }
        } catch (\Exception $e) {
            // Silently fail if DB not migrated yet
        }
    }
}
