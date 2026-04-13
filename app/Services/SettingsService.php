<?php

namespace App\Services;

use App\HelperClass;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;
use App\Models\PolicySetting;

class SettingsService
{
    /**
     * Update policy settings.
     */
    public function updatePolicySettings(array $data): PolicySetting
    {
        $setting = PolicySetting::first() ?? new PolicySetting;
        $setting->fill($data);
        $setting->save();

        return $setting;
    }

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
            'notify_email',
            'login_banner',
            'register_banner',
            'primary_color',
            'secondary_color',
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

        if (isset($data['login_banner'])) {
            if ($setting->login_banner) {
                HelperClass::file_delete($setting->login_banner);
            }
            $updateData['login_banner'] = HelperClass::file_upload($data['login_banner'], 'settings');
        }

        if (isset($data['register_banner'])) {
            if ($setting->register_banner) {
                HelperClass::file_delete($setting->register_banner);
            }
            $updateData['register_banner'] = HelperClass::file_upload($data['register_banner'], 'settings');
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
     * Update contact settings.
     */
    public function updateContactSettings(array $data): ContactSetting
    {
        $setting = ContactSetting::first() ?? new ContactSetting;
        $setting->fill($data);
        $setting->save();

        return $setting;
    }
}
