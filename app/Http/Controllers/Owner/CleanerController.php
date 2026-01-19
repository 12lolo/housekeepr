<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Cleaner;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CleanerController extends Controller
{
    /**
     * Display a listing of cleaners (UC-O1).
     */
    public function index()
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        $cleaners = $hotel->cleaners()
            ->with('user')
            ->withCount(['cleaningTasks' => function ($query) {
                $query->where('date', '>=', today());
            }])
            ->orderBy('status', 'asc')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('owner.cleaners.index', compact('cleaners'));
    }

    /**
     * Show the form for creating a new cleaner (UC-O2).
     */
    public function create()
    {
        Gate::authorize('manage-hotel');

        return view('owner.cleaners.create');
    }

    /**
     * Store a newly created cleaner (UC-O3, UC-O4, UC-O5).
     */
    public function store(Request $request)
    {
        Gate::authorize('manage-hotel');

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        // UC-O3: Validate unique email
        // UC-O4: Validate name
        $validated = $request->validate([
            'email' => 'required|email|unique:users,email',
            'name' => 'required|string|min:2|max:255',
            'phone' => 'nullable|regex:/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/|max:20',
            'availability' => 'required|array|min:1',
            'availability.*' => 'integer|between:0,6',
        ]);

        // Generate temporary password
        $tempPassword = Str::random(12);

        // Create user for cleaner
        $cleanerUser = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($tempPassword),
            'role' => 'cleaner',
            'status' => 'pending',
        ]);

        // Mark email as verified separately (avoid mass assignment protection)
        $cleanerUser->email_verified_at = now();
        $cleanerUser->save();

        // Link cleaner to hotel (UC-O5)
        $cleaner = Cleaner::create([
            'user_id' => $cleanerUser->id,
            'hotel_id' => $hotel->id,
            'status' => 'pending', // Pending until first login
        ]);

        // Save availability
        foreach ($validated['availability'] as $dayOfWeek) {
            $cleaner->availability()->create([
                'day_of_week' => $dayOfWeek,
            ]);
        }

        activity()
            ->performedOn($cleaner)
            ->causedBy($user)
            ->log('Schoonmaker toegevoegd: '.$cleanerUser->name);

        // TODO: Send welcome email with credentials
        // Mail::to($cleanerUser->email)->send(new CleanerWelcomeMail($cleanerUser, $tempPassword));

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Schoonmaker toegevoegd.',
                'temp_password' => $tempPassword,
                'cleaner' => $cleaner->load('user'),
            ]);
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Schoonmaker toegevoegd. Tijdelijk wachtwoord: '.$tempPassword.' (verstuur dit apart!)');
    }

    /**
     * Display the specified cleaner.
     */
    public function show(Cleaner $cleaner)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($cleaner->hotel_id !== $hotel->id) {
            abort(403);
        }

        $cleaner->load([
            'user',
            'cleaningTasks' => function ($query) {
                $query->where('date', '>=', today()->subDays(7))
                    ->with(['room', 'booking'])
                    ->orderBy('date', 'desc');
            },
        ]);

        return view('owner.cleaners.show', compact('cleaner'));
    }

    /**
     * Show the form for editing a cleaner.
     */
    public function edit(Cleaner $cleaner)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($cleaner->hotel_id !== $hotel->id) {
            abort(403);
        }

        return view('owner.cleaners.edit', compact('cleaner'));
    }

    /**
     * Update the specified cleaner.
     */
    public function update(Request $request, Cleaner $cleaner)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($cleaner->hotel_id !== $hotel->id) {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|unique:users,email,'.$cleaner->user_id,
            'phone' => 'nullable|regex:/^[\+]?[(]?[0-9]{1,4}[)]?[-\s\.]?[(]?[0-9]{1,4}[)]?[-\s\.]?[0-9]{1,9}$/|max:20',
            'availability' => 'required|array|min:1',
            'availability.*' => 'integer|between:0,6',
        ]);

        // Update user
        $cleaner->user->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
        ]);

        // Update availability - delete old and create new
        $cleaner->availability()->delete();
        foreach ($validated['availability'] as $dayOfWeek) {
            $cleaner->availability()->create([
                'day_of_week' => $dayOfWeek,
            ]);
        }

        activity()
            ->performedOn($cleaner)
            ->causedBy($user)
            ->log('Schoonmaker bijgewerkt: '.$cleaner->user->name);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Schoonmaker bijgewerkt.',
                'cleaner' => $cleaner->load('user'),
            ]);
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Schoonmaker bijgewerkt.');
    }

    /**
     * Remove the specified cleaner from storage.
     */
    public function destroy(Request $request, Cleaner $cleaner)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($cleaner->hotel_id !== $hotel->id) {
            abort(403);
        }

        // Check for active tasks
        $activeTasks = $cleaner->cleaningTasks()
            ->where('status', '!=', 'completed')
            ->where('date', '>=', today())
            ->count();

        if ($activeTasks > 0) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Kan schoonmaker niet verwijderen: er zijn nog '.$activeTasks.' actieve taken.',
                ], 400);
            }

            return back()->with('error', 'Kan schoonmaker niet verwijderen: er zijn nog '.$activeTasks.' actieve taken.');
        }

        $cleanerName = $cleaner->user->name;
        $cleanerUser = $cleaner->user;

        $cleaner->delete();
        $cleanerUser->delete();

        activity()
            ->causedBy($user)
            ->log('Schoonmaker verwijderd: '.$cleanerName);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Schoonmaker verwijderd.',
            ]);
        }

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Schoonmaker verwijderd.');
    }

    /**
     * Deactivate a cleaner (UC-O6).
     */
    public function deactivate(Cleaner $cleaner)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($cleaner->hotel_id !== $hotel->id) {
            abort(403);
        }

        if ($cleaner->status === 'deactivated') {
            return back()->with('error', 'Schoonmaker is al gedeactiveerd.');
        }

        // Check for active tasks
        $activeTasks = $cleaner->cleaningTasks()
            ->where('status', '!=', 'completed')
            ->where('date', '>=', today())
            ->count();

        if ($activeTasks > 0) {
            return back()->with('error', 'Kan schoonmaker niet deactiveren: er zijn nog '.$activeTasks.' actieve taken.');
        }

        $cleaner->update(['status' => 'deactivated']);
        $cleaner->user->update(['status' => 'deactivated']);

        activity()
            ->performedOn($cleaner)
            ->causedBy($user)
            ->log('Schoonmaker gedeactiveerd');

        return back()->with('success', 'Schoonmaker gedeactiveerd.');
    }

    /**
     * Activate a cleaner.
     */
    public function activate(Cleaner $cleaner)
    {
        Gate::authorize('manage-hotel');

        // Verify ownership
        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        if ($cleaner->hotel_id !== $hotel->id) {
            abort(403);
        }

        if ($cleaner->status === 'active') {
            return back()->with('error', 'Schoonmaker is al actief.');
        }

        $cleaner->update(['status' => 'active']);
        $cleaner->user->update(['status' => 'active']);

        activity()
            ->performedOn($cleaner)
            ->causedBy($user)
            ->log('Schoonmaker geactiveerd');

        return back()->with('success', 'Schoonmaker geactiveerd.');
    }
}
