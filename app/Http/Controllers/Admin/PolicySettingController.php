<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdatePolicySettingRequest;
use App\Models\PolicySetting;
use App\Services\SettingsService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class PolicySettingController extends Controller
{
    public function __construct(protected SettingsService $settingsService) {}

    /**
     * Show the form for editing policy settings.
     */
    public function edit(): View
    {
        $setting = PolicySetting::first();

        return view('admin.settings.policies', compact('setting'));
    }

    /**
     * Update the policy settings in storage.
     */
    public function update(UpdatePolicySettingRequest $request): RedirectResponse
    {
        $this->settingsService->updatePolicySettings($request->validated());

        return back()->with([
            'message' => 'Policy settings updated successfully',
            'alert-type' => 'success',
        ]);
    }
}
