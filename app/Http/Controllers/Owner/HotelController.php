<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use Illuminate\Http\Request;

class HotelController extends Controller
{
    /**
     * Store a newly created hotel by the owner.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|min:2|max:255',
            'street' => 'required|string|max:255',
            'house_number' => 'required|string|max:20',
            'house_number_addition' => 'nullable|string|max:10',
            'postal_code' => 'required|string|max:20',
            'city' => 'required|string|max:100',
            'country' => 'required|string|max:100',
        ]);

        $owner = auth()->user();

        // Check if owner already has a hotel
        if ($owner->hotel) {
            return back()->with('error', 'Je hebt al een hotel aangemaakt.');
        }

        // Build full address
        $address = sprintf(
            '%s %s%s, %s %s, %s',
            $validated['street'],
            $validated['house_number'],
            $validated['house_number_addition'] ? ' ' . $validated['house_number_addition'] : '',
            $validated['postal_code'],
            $validated['city'],
            $validated['country']
        );

        // Create hotel
        $hotel = Hotel::create([
            'name' => $validated['name'],
            'address' => $address,
            'owner_id' => $owner->id,
            // Store individual address components
            'street' => $validated['street'],
            'house_number' => $validated['house_number'],
            'house_number_addition' => $validated['house_number_addition'] ?? null,
            'postal_code' => $validated['postal_code'],
            'city' => $validated['city'],
            'country' => $validated['country'],
        ]);

        activity()
            ->performedOn($hotel)
            ->causedBy($owner)
            ->log('Hotel aangemaakt: ' . $hotel->name);

        return redirect()
            ->route('owner.dashboard')
            ->with('success', 'Hotel succesvol aangemaakt! Je kunt nu kamers en schoonmakers toevoegen.');
    }
}

