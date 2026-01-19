<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Gate;

class PlannerController extends Controller
{
    /**
     * Manually trigger planner for a specific date (UC-P2).
     */
    public function runPlanner(Request $request)
    {
        Gate::authorize('manage-hotel');

        $validated = $request->validate([
            'date' => 'required|date',
        ]);

        $user = auth()->user();
        $hotel = $user->role === 'owner' ? $user->hotel : $user->cleaner->hotel;

        try {
            // Run planner for specific hotel and date
            $exitCode = Artisan::call('hcs:plan-tasks', [
                '--hotel' => $hotel->id,
                '--date' => $validated['date'],
                '--days' => 1,
                '--force' => true,
            ]);

            // Get command output
            $output = Artisan::output();

            activity()
                ->causedBy($user)
                ->withProperties([
                    'hotel_id' => $hotel->id,
                    'date' => $validated['date'],
                    'exit_code' => $exitCode,
                ])
                ->log('Handmatige planning uitgevoerd voor '.$validated['date']);

            if ($exitCode === 0) {
                return back()->with('success', 'Planning succesvol uitgevoerd voor '.Carbon::parse($validated['date'])->format('d-m-Y').'.');
            } else {
                return back()->with('error', 'Planning mislukt. Probeer het later opnieuw.');
            }
        } catch (\Exception $e) {
            activity()
                ->causedBy($user)
                ->withProperties([
                    'hotel_id' => $hotel->id,
                    'date' => $validated['date'],
                    'error' => $e->getMessage(),
                ])
                ->log('Handmatige planning MISLUKT');

            return back()->with('error', 'Er is een fout opgetreden: '.$e->getMessage());
        }
    }
}
