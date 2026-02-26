<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(Request $request): View
    {
        $title = 'Login';
        $section = 'login';
        if ($request->route('type') == 'admin') {
            return view('auth.login');
        }elseif($request->route('type') == 'user'){
            return view('client.auth.login', compact('title', 'section'));
        }else{
            abort(404, 'Page not found');
        }
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        if ($request->route('type') == 'admin') {
            return redirect()->route('dashboard');
        }elseif($request->route('type') == 'user'){
            return redirect()->route('home');
        }else{
            return redirect()->route()->back();
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
