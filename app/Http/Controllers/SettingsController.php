<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactSettingRequest;
use App\Http\Requests\GeneralSettingRequest;
use App\Http\Requests\MailSettingRequest;
use App\Http\Requests\SocialLoginSettingRequest;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;
use App\Models\MailSetting;
use App\Models\SocialLoginSetting;

class SettingsController extends Controller
{
    public function __construct(protected SettingsService $settingsService) {}

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

    public function contactSettings(): View
    {
        $setting = ContactSetting::first();

        return view('admin.settings.contact', compact('setting'));
    }

    public function socialLoginSettings(): View
    {
        $setting = SocialLoginSetting::first() ?? new SocialLoginSetting;

        return view('admin.settings.social-login', compact('setting'));
    }

    public function updateGeneralSettings(GeneralSettingRequest $request): RedirectResponse
    {
        $this->settingsService->updateGeneralSettings($request->validated());

        return back()->with([
            'message' => 'General settings updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function updateMailSettings(MailSettingRequest $request): RedirectResponse
    {
        $this->settingsService->updateMailSettings($request->validated());

        return back()->with([
            'message' => 'Mail settings updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function updateContactSettings(ContactSettingRequest $request): RedirectResponse
    {
        $this->settingsService->updateContactSettings($request->validated());

        return back()->with([
            'message' => 'Contact settings updated successfully',
            'alert-type' => 'success',
        ]);
    }

    public function updateSocialLoginSettings(SocialLoginSettingRequest $request): RedirectResponse
    {
        $this->settingsService->updateSocialLoginSettings($request->validated());

        return back()->with([
            'message' => 'Social login settings updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
