<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\UpdateAdminAvatarRequest;
use App\Http\Requests\Admin\UpdateAdminDetailsRequest;
use App\Http\Requests\Admin\UpdateAdminPasswordRequest;
use App\Services\AdminProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminProfileController extends Controller
{
    public function __construct(protected AdminProfileService $profileService) {}

    /**
     * Display the logged-in administrator's profile.
     */
    public function show(): View
    {
        $admin = Auth::guard('admin')->user();
        $admin->load('roles');

        return view('admin.profile.show', compact('admin'));
    }

    /**
     * Update basic profile details.
     */
    public function updateDetails(UpdateAdminDetailsRequest $request): RedirectResponse
    {
        $id = Auth::guard('admin')->id();
        $this->profileService->updateDetails($id, $request->validated());

        return redirect()->route('admin.profile.show')->with([
            'message' => 'Profile details updated successfully.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Update profile password.
     */
    public function updatePassword(UpdateAdminPasswordRequest $request): RedirectResponse
    {
        $id = Auth::guard('admin')->id();
        $this->profileService->updatePassword($id, $request->password);

        return redirect()->route('admin.profile.show')->with([
            'message' => 'Password changed successfully.',
            'alert-type' => 'success',
        ]);
    }

    /**
     * Update profile avatar.
     */
    public function updateAvatar(UpdateAdminAvatarRequest $request): RedirectResponse
    {
        $id = Auth::guard('admin')->id();
        $this->profileService->updateAvatar($id, $request->file('avatar'));

        return redirect()->route('admin.profile.show')->with([
            'message' => 'Profile image updated successfully.',
            'alert-type' => 'success',
        ]);
    }
}
