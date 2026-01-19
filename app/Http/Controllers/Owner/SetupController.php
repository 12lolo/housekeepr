<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class SetupController extends Controller
{
    /**
     * Show the account setup form.
     */
    public function showAccountSetup()
    {
        return view('owner.setup-account');
    }

    /**
     * Process the account setup form.
     */
    public function storeAccountSetup(Request $request)
    {
        $user = auth()->user();

        // Validate the request
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'current_password' => ['nullable', 'string'],
            'password' => ['nullable', 'confirmed', Password::defaults()],
        ]);

        // If password_confirmation is filled, password must be filled
        if ($request->filled('password_confirmation') && ! $request->filled('password')) {
            return back()->withErrors([
                'password' => 'Je moet een nieuw wachtwoord invoeren om het te bevestigen.',
            ])->withInput();
        }

        // If user wants to change password, verify current password
        if ($request->filled('password')) {
            if (! $request->filled('current_password')) {
                return back()->withErrors([
                    'current_password' => 'Je moet je huidige wachtwoord invoeren om een nieuw wachtwoord in te stellen.',
                ])->withInput();
            }

            if (! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors([
                    'current_password' => 'Het huidige wachtwoord is onjuist.',
                ])->withInput();
            }

            // Update password
            $user->password = Hash::make($request->password);
        }

        // Update user name
        $user->name = $validated['name'];

        // Mark email as verified
        if (! $user->email_verified_at) {
            $user->email_verified_at = now();
        }

        // Change status from pending to active
        if ($user->status === 'pending') {
            $user->status = 'active';
        }

        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('Account setup voltooid');

        // Redirect to hotel setup
        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Account succesvol ingesteld! Je kunt nu je hotel aanmaken.');
    }
}
