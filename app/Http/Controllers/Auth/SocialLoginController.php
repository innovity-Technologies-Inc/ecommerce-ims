<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        $setting = \App\Models\SocialLoginSetting::first();
        if (! $setting || ! $setting->google_status) {
            return redirect()->route('login')->withErrors(['error' => 'Google Login is currently disabled.']);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                // 'status' => 1, // Ensure user is active
            ]);

            Auth::login($user);

            return redirect()->intended(route('home'));

        } catch (Exception $e) {
            return redirect()->route('login')->withErrors(['error' => 'Google Login failed. Please try again.']);
        }
    }
}
