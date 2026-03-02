<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmailVerificationPromptController extends Controller
{
    /**
     * Display the email verification prompt.
     */
    public function __invoke(Request $request): RedirectResponse|View
    {
        $title = 'Verify Your Email Address';
        $section = 'Email Verification';

        return $request->user()->hasVerifiedEmail()
                    ? redirect()->route('home')->with([
                        'message' => 'Your email address has been verified. Enjoy Shopping!',
                'alert-type' => 'success',
            ]) : view('client.auth.verify-email', compact('title', 'section'));
    }
}
