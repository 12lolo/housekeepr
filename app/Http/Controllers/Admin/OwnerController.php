<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Hotel;
use App\Mail\OwnerInviteMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class OwnerController extends Controller
{
    /**
     * Display a listing of owners (UC-A1).
     */
    public function index()
    {
        $owners = User::where('role', 'owner')
            ->with('hotel')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.owners.index', compact('owners'));
    }

    /**
     * Show the form for creating a new owner (UC-A2).
     */
    public function create()
    {
        return view('admin.owners.create');
    }

    /**
     * Store a newly created owner and send invitation (UC-A3, UC-A4, UC-A5).
     */
    public function store(Request $request)
    {
        // UC-A3: Validate unique email
        // UC-A4: Validate hotel name
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'hotel_name' => 'required|string|min:2|max:255',
            'name' => 'required|string|min:2|max:255',
        ]);

        // Generate temporary password
        $tempPassword = Str::random(16);

        // Create owner user with pending status
        $owner = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($tempPassword),
            'role' => 'owner',
            'status' => 'pending',
        ]);

        // Create hotel and link to owner (UC-A6)
        $hotel = Hotel::create([
            'name' => $validated['hotel_name'],
            'owner_id' => $owner->id,
        ]);

        // Log activity
        activity()
            ->performedOn($owner)
            ->causedBy(auth()->user())
            ->log('Eigenaar uitgenodigd met hotel: ' . $hotel->name);

        // Send invitation email (UC-A5)
        try {
            Mail::to($owner->email)->send(new OwnerInviteMail($owner, $hotel, $tempPassword));
        } catch (\Exception $e) {
            // Rollback if email fails
            $hotel->delete();
            $owner->delete();

            if ($request->expectsJson() || $request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Versturen van uitnodiging mislukt. Probeer het later opnieuw.'
                ], 500);
            }

            return back()
                ->withInput()
                ->with('error', 'Versturen van uitnodiging mislukt. Probeer het later opnieuw.');
        }

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Eigenaar uitgenodigd. Activatielink verstuurd naar ' . $owner->email,
                'owner' => $owner->load('hotels')
            ]);
        }

        return redirect()
            ->route('admin.owners.index')
            ->with('success', 'Eigenaar uitgenodigd. Activatielink verstuurd naar ' . $owner->email);
    }

    /**
     * Show the specified owner.
     */
    public function show(Request $request, User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        $owner->load(['hotels']);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'id' => $owner->id,
                'name' => $owner->name,
                'email' => $owner->email,
                'status' => $owner->status,
                'hotels_count' => $owner->hotels->count(),
                'created_at' => $owner->created_at,
            ]);
        }

        return view('admin.owners.show', compact('owner'));
    }

    /**
     * Show the form for editing the specified owner.
     */
    public function edit(Request $request, User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        if ($request->expectsJson() || $request->ajax()) {
            return view('admin.owners.edit-form', compact('owner'))->render();
        }

        return view('admin.owners.edit', compact('owner'));
    }

    /**
     * Update the specified owner.
     */
    public function update(Request $request, User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($owner->id)],
            'hotel_name' => 'required|string|min:2|max:255',
        ]);

        $owner->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
        ]);

        // Update first hotel if exists
        $hotel = $owner->hotels()->first();
        if ($hotel) {
            $hotel->update([
                'name' => $validated['hotel_name'],
            ]);
        }

        activity()
            ->performedOn($owner)
            ->causedBy(auth()->user())
            ->log('Eigenaar bijgewerkt');

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Eigenaar succesvol bijgewerkt',
                'owner' => $owner->load('hotels')
            ]);
        }

        return redirect()
            ->route('admin.owners.index')
            ->with('success', 'Eigenaar bijgewerkt.');
    }

    /**
     * Deactivate owner (UC-A7).
     */
    public function deactivate(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        if ($owner->status === 'deactivated') {
            return back()->with('error', 'Eigenaar is al gedeactiveerd.');
        }

        $owner->update(['status' => 'deactivated']);

        activity()
            ->performedOn($owner)
            ->causedBy(auth()->user())
            ->log('Eigenaar gedeactiveerd');

        return back()->with('success', 'Eigenaar gedeactiveerd. Kan niet meer inloggen.');
    }

    /**
     * Activate owner (UC-A8).
     */
    public function activate(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        if ($owner->status === 'active') {
            return back()->with('error', 'Eigenaar is al actief.');
        }

        $owner->update(['status' => 'active']);

        activity()
            ->performedOn($owner)
            ->causedBy(auth()->user())
            ->log('Eigenaar geactiveerd');

        return back()->with('success', 'Eigenaar geactiveerd. Kan weer inloggen.');
    }

    /**
     * Remove the specified owner from storage.
     */
    public function destroy(User $owner)
    {
        if ($owner->role !== 'owner') {
            abort(404);
        }

        activity()
            ->performedOn($owner)
            ->causedBy(auth()->user())
            ->log('Eigenaar verwijderd: ' . $owner->name);

        $owner->delete();

        return redirect()
            ->route('admin.owners.index')
            ->with('success', 'Eigenaar verwijderd.');
    }
}
