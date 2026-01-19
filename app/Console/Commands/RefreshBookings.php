<?php

namespace App\Console\Commands;

use App\Models\Booking;
use App\Models\Hotel;
use App\Models\Issue;
use Illuminate\Console\Command;

class RefreshBookings extends Command
{
    protected $signature = 'housekeepr:refresh-bookings {hotel_id? : The ID of the hotel (optional, refreshes all if not provided)} {--keep-existing : Keep existing bookings instead of deleting them}';

    protected $description = 'Delete old bookings and create new ones with current dates';

    public function handle()
    {
        $hotelId = $this->argument('hotel_id');

        if (! $this->option('keep-existing')) {
            if ($hotelId) {
                $hotel = Hotel::find($hotelId);
                if (! $hotel) {
                    $this->error("Hotel with ID {$hotelId} not found!");

                    return 1;
                }
                $this->info("Deleting old bookings for {$hotel->name}...");
                $deletedCount = Booking::whereHas('room', function ($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId);
                })->count();
                Booking::whereHas('room', function ($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId);
                })->delete();
            } else {
                $this->info('Deleting old bookings for ALL hotels...');
                $deletedCount = Booking::count();
                Booking::query()->delete();
            }
            $this->info("✅ Deleted {$deletedCount} old bookings");
            $this->newLine();

            // Also clean up auto-generated issues
            $this->info('Cleaning up auto-generated issues...');
            $issuesQuery = Issue::where('note', 'like', 'URGENT:%');
            if ($hotelId) {
                $issuesQuery->whereHas('room', function ($q) use ($hotelId) {
                    $q->where('hotel_id', $hotelId);
                });
            }
            $issuesCount = $issuesQuery->count();
            if ($issuesCount > 0) {
                $issuesQuery->delete();
                $this->info("✅ Deleted {$issuesCount} auto-generated issue(s)");
            } else {
                $this->info('  No auto-generated issues to clean');
            }
            $this->newLine();
        }

        $this->info('Creating new bookings with current dates...');

        $hotelsQuery = Hotel::with('rooms');
        if ($hotelId) {
            $hotelsQuery->where('id', $hotelId);
        }
        $hotels = $hotelsQuery->get();

        if ($hotels->isEmpty()) {
            $this->error('No hotels found!');

            return 1;
        }

        $created = 0;
        $today = now();

        foreach ($hotels as $hotel) {
            $rooms = $hotel->rooms;

            if ($rooms->isEmpty()) {
                $this->warn("  ⚠️  {$hotel->name} has no rooms, skipping");

                continue;
            }

            $this->info("Creating bookings for {$hotel->name}...");

            // Create bookings for the next 7 days
            for ($day = 0; $day < 7; $day++) {
                $checkInDate = $today->copy()->addDays($day);
                $checkOutDate = $checkInDate->copy()->addDays(rand(1, 3)); // Stay 1-3 nights

                // Random check-in time (usually 14:00-18:00)
                $checkInTime = $checkInDate->copy()->setHour(rand(14, 18))->setMinute(rand(0, 59));
                $checkOutTime = $checkOutDate->copy()->setHour(11)->setMinute(0); // Checkout at 11:00

                // Pick 2-3 random rooms for this day
                $numBookings = min(rand(2, 3), $rooms->count());
                $selectedRooms = $rooms->random($numBookings);

                foreach ($selectedRooms as $room) {
                    $notes = rand(0, 3) > 0 ? null : 'Extra handdoeken graag';

                    Booking::create([
                        'room_id' => $room->id,
                        'guest_name' => $this->generateGuestName(),
                        'check_in' => $checkInDate->toDateString(),
                        'check_out' => $checkOutDate->toDateString(),
                        'check_in_datetime' => $checkInTime,
                        'check_out_datetime' => $checkOutTime,
                        'notes' => $notes,
                    ]);

                    $created++;
                }
            }

            $this->info("  ✅ Created bookings for {$hotel->name}");
        }

        $this->newLine();
        $this->info("✅ Complete! Created {$created} new bookings");
        $this->info('   Each booking will automatically create a cleaning task!');

        return 0;
    }

    private function generateGuestName(): string
    {
        $firstNames = ['Jan', 'Emma', 'Lucas', 'Sophie', 'Thomas', 'Lisa', 'Mike', 'Anna', 'David', 'Maria'];
        $lastNames = ['de Vries', 'Jansen', 'Bakker', 'Visser', 'Smit', 'Meijer', 'de Boer', 'Mulder', 'de Groot', 'Bos'];

        return $firstNames[array_rand($firstNames)].' '.$lastNames[array_rand($lastNames)];
    }
}
