<?php

namespace App\Services;

use App\HelperClass;
use App\Models\GeneralSetting;
use App\Models\MailSetting;

class SettingsService
{
    /**
     * Update general settings.
     */
    public function updateGeneralSettings(array $data): GeneralSetting
    {
        $setting = GeneralSetting::first() ?? new GeneralSetting;

        $updateData = collect($data)->only([
            'business_name',
            'meta_title',
            'meta_description',
            'currency',
        ])->toArray();

        if (isset($data['dark_logo'])) {
            if ($setting->dark_logo) {
                HelperClass::file_delete($setting->dark_logo);
            }
            $updateData['dark_logo'] = HelperClass::file_upload($data['dark_logo'], 'settings');
        }

        if (isset($data['light_logo'])) {
            if ($setting->light_logo) {
                HelperClass::file_delete($setting->light_logo);
            }
            $updateData['light_logo'] = HelperClass::file_upload($data['light_logo'], 'settings');
        }

        if (isset($data['breadcrumb_image'])) {
            if ($setting->breadcrumb_image) {
                HelperClass::file_delete($setting->breadcrumb_image);
            }
            $updateData['breadcrumb_image'] = HelperClass::file_upload($data['breadcrumb_image'], 'settings');
        }

        if (isset($data['favicon'])) {
            if ($setting->favicon) {
                HelperClass::file_delete($setting->favicon);
            }
            $updateData['favicon'] = HelperClass::file_upload($data['favicon'], 'settings');
        }

        $setting->fill($updateData);
        $setting->save();

        return $setting;
    }

    /**
     * Update mail settings.
     */
    public function updateMailSettings(array $data): MailSetting
    {
        $setting = MailSetting::first() ?? new MailSetting;
        $setting->fill($data);
        $setting->save();

        return $setting;
    }
}
