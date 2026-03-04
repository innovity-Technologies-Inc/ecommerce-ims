<?php

namespace App\Http\Controllers;

use App\HelperClass;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function generalSettings(): View
    {
        $setting = GeneralSetting::first();

        return view('admin.settings.general', compact('setting'));
    }

    public function mailSettings(): View
    {
        $setting = MailSetting::first();

        return view('admin.settings.mail', compact('setting'));
    }

    public function updateMailSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'mail_mailer' => ['required', 'string', 'max:255'],
            'mail_host' => ['required', 'string', 'max:255'],
            'mail_port' => ['required', 'string', 'max:255'],
            'mail_username' => ['required', 'string', 'max:255'],
            'mail_password' => ['required', 'string', 'max:255'],
            'mail_encryption' => ['nullable', 'string', 'max:255'],
            'mail_from_address' => ['required', 'email', 'max:255'],
            'mail_from_name' => ['required', 'string', 'max:255'],
        ]);

        $setting = MailSetting::first() ?? new MailSetting;
        $setting->fill($request->all());
        $setting->save();

        return back()->with([
            'message' => 'Mail settings updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function updateGeneralSettings(Request $request): RedirectResponse
    {
        $request->validate([
            'business_name' => ['nullable', 'string', 'max:255'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'currency' => ['nullable', 'string', 'max:10'],
            'dark_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'light_logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'breadcrumb_image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:2048'],
            'favicon' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,svg,webp', 'max:1024'],
        ]);

        $setting = GeneralSetting::first() ?? new GeneralSetting;

        $data = $request->only([
            'business_name',
            'meta_title',
            'meta_description',
            'currency',
        ]);

        if ($request->hasFile('dark_logo')) {
            if ($setting->dark_logo) {
                HelperClass::file_delete($setting->dark_logo);
            }
            $data['dark_logo'] = HelperClass::file_upload($request->file('dark_logo'), 'settings');
        }

        if ($request->hasFile('light_logo')) {
            if ($setting->light_logo) {
                HelperClass::file_delete($setting->light_logo);
            }
            $data['light_logo'] = HelperClass::file_upload($request->file('light_logo'), 'settings');
        }

        if ($request->hasFile('breadcrumb_image')) {
            if ($setting->breadcrumb_image) {
                HelperClass::file_delete($setting->breadcrumb_image);
            }
            $data['breadcrumb_image'] = HelperClass::file_upload($request->file('breadcrumb_image'), 'settings');
        }

        if ($request->hasFile('favicon')) {
            if ($setting->favicon) {
                HelperClass::file_delete($setting->favicon);
            }
            $data['favicon'] = HelperClass::file_upload($request->file('favicon'), 'settings');
        }

        $setting->fill($data);
        $setting->save();

        return back()->with([
            'message' => 'General settings updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
