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
        return view('auth.login', [
            'redirect' => $request->query('redirect', route('home', absolute: false)),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Mengubah fallback route default dari 'dashboard' ke 'home'
        $redirectTo = $request->input('redirect', route('home', absolute: false));

        // Jika user adalah merchant dan tidak sedang mengakses link redirect khusus
        if (method_exists(Auth::user(), 'isMerchant') && Auth::user()->isMerchant() && !$request->has('redirect')) {
            return redirect()->intended(route('merchant.dashboard', absolute: false));
        }

        return redirect()->intended($redirectTo);
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
} Everything pro