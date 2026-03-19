<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(Request $request): View
    {
        $title = 'Registration';
        $section = 'User Registration';

        return view('client.auth.register', compact('title', 'section'));
    }

    public function store(Request $request)
    {
        // Base validation (for both)
        $rules = [
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'mobile' => ['required', 'string', 'max:20'],
        ];

        $validated = $request->validate($rules);

        $user = User::create([
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'mobile' => $validated['mobile'],
        ]);

        $oldSessionId = $request->session()->getId();
        Auth::login($user);

        app(\App\Services\CartService::class)->syncCartOnLogin($oldSessionId);

        // 🔥 Send email verification ONLY for user
        try {
            event(new Registered($user));
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Registration Email Error: '.$e->getMessage());

            return redirect()->route('verification.notice')->with([
                'message' => 'Registration successful, but we could not send the verification email right now. Please try again later from your profile.',
                'alert-type' => 'warning',
            ]);
        }

        return redirect()->route('verification.notice');

    }
}
