<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\ContactSettingRequest;
use App\Http\Requests\Admin\GeneralSettingRequest;
use App\Models\ContactSetting;
use App\Models\GeneralSetting;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function __construct(protected SettingsService $settingsService) {}

    public function generalSettings(): View
    {
        $setting = GeneralSetting::first();

        return view('admin.settings.general', compact('setting'));
    }

    public function contactSettings(): View
    {
        $setting = ContactSetting::first();

        return view('admin.settings.contact', compact('setting'));
    }

    public function updateGeneralSettings(GeneralSettingRequest $request): RedirectResponse
    {
        $this->settingsService->updateGeneralSettings($request->validated());

        return back()->with([
            'message' => 'General settings updated successfully',
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
}
