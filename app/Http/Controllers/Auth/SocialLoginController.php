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
        try {
            $googleUser = Socialite::driver('google')->user();

            $oldSessionId = session()->getId();

            // Check if user already exists and is inactive
            $existingUser = User::where('email', $googleUser->email)->first();

            if ($existingUser && ! $existingUser->status) {
                return redirect()->route('login')->withErrors(['error' => 'Your account is inactive. Please contact support.']);
            }

            $user = User::updateOrCreate([
                'email' => $googleUser->email,
            ], [
                'name' => $googleUser->name,
                'google_id' => $googleUser->id,
                'google_token' => $googleUser->token,
                'status' => $existingUser ? $existingUser->status : 1, // Keep status if exists, else Active
                'email_verified_at' => $existingUser ? $existingUser->email_verified_at : now(),
            ]);

            Auth::login($user);

            // Sync Cart
            app(\App\Services\CartService::class)->syncCartOnLogin($oldSessionId);

            session()->regenerate();

            return redirect()->route('home')->with([
                'message' => 'You are now logged in',
                'alert-type' => 'success',
            ]);

        } catch (Exception $e) {
            \Illuminate\Support\Facades\Log::error('Google Login Error: '.$e->getMessage(), ['exception' => $e]);

            return redirect()->route('login')->withErrors(['error' => 'Google Login failed. Please try again.']);
        }
    }
}
