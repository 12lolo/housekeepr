<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * Update the user's name.
     */
    public function updateName(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
        ]);

        $user = auth()->user();
        $user->name = $validated['name'];
        $user->save();

        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->log('Naam gewijzigd naar: ' . $user->name);

        return redirect()->back()->with('success', 'Naam succesvol gewijzigd.');
    }
}

