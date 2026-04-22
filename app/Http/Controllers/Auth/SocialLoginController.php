<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;

class SocialLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        if (! config('services.google.client_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Google Login is currently disabled.']);
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function handleGoogleCallback()
    {
        return $this->handleSocialCallback('google');
    }

    /**
     * Redirect the user to the Facebook authentication page.
     */
    public function redirectToFacebook()
    {
        if (! config('services.facebook.client_id')) {
            return redirect()->route('login')->withErrors(['error' => 'Facebook Login is currently disabled.']);
        }

        return Socialite::driver('facebook')
            ->scopes(['public_profile', 'email'])
            ->redirect();
    }

    /**
     * Obtain the user information from Facebook.
     */
    public function handleFacebookCallback()
    {
        return $this->handleSocialCallback('facebook');
    }

    /**
     * Common logic for handling social callbacks.
     */
    protected function handleSocialCallback(string $driver)
    {
        try {
            $socialUser = Socialite::driver($driver)->user();

            $oldSessionId = session()->getId();

            // Check if user already exists and is inactive
            $existingUser = User::where('email', $socialUser->email)->first();

            if ($existingUser && ! $existingUser->status) {
                return redirect()->route('login')->withErrors(['error' => 'Your account is inactive. Please contact support.']);
            }

            $userData = [
                'name' => $socialUser->name,
                'status' => $existingUser ? $existingUser->status : 1, // Keep status if exists, else Active
                'email_verified_at' => $existingUser ? $existingUser->email_verified_at : now(),
            ];

            // Handle User Photo
            if ($socialUser->avatar) {
                // If user already has an image, delete it first
                if ($existingUser && $existingUser->image) {
                    \App\HelperClass::file_delete($existingUser->image);
                }
                
                // Download and save social photo
                $photoPath = \App\HelperClass::file_upload_from_url($socialUser->avatar, 'customers');
                if ($photoPath) {
                    $userData['image'] = $photoPath;
                }
            }

            if ($driver === 'google') {
                $userData['google_id'] = $socialUser->id;
                $userData['google_token'] = $socialUser->token;
            } elseif ($driver === 'facebook') {
                $userData['facebook_id'] = $socialUser->id;
                $userData['facebook_token'] = $socialUser->token;
            }

            $user = User::updateOrCreate([
                'email' => $socialUser->email,
            ], $userData);

            Auth::login($user);

            // Sync Cart
            app(\App\Services\CartService::class)->syncCartOnLogin($oldSessionId);

            session()->regenerate();

            return redirect()->route('home')->with([
                'message' => 'You are now logged in',
                'alert-type' => 'success',
            ]);

        } catch (Exception $e) {
            Log::error(ucfirst($driver).' Login Error: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->route('login')->withErrors(['error' => ucfirst($driver).' Login failed. Please try again.']);
        }
    }
}
