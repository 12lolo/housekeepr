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
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = auth()->user();

        // Activate pending cleaners on first login
        if ($user->role === 'cleaner' && $user->status === 'pending') {
            $user->status = 'active';
            $user->save();

            // Also update the cleaner record
            if ($user->cleaner) {
                $user->cleaner->status = 'active';
                $user->cleaner->save();

                activity()
                    ->causedBy($user)
                    ->performedOn($user->cleaner)
                    ->log('Schoonmaker geactiveerd bij eerste login');
            }
        }

        // Log successful login
        activity()
            ->causedBy($user)
            ->withProperties([
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
            ])
            ->log('Succesvol ingelogd');

        return redirect()->intended(route('dashboard', absolute: false));
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = auth()->user();

        // Log logout before destroying session
        if ($user) {
            activity()
                ->causedBy($user)
                ->withProperties([
                    'ip_address' => $request->ip(),
                ])
                ->log('Uitgelogd');
        }

        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
