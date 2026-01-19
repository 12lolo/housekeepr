<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "Direct Booking Test\n";
echo "===================\n\n";
// Get hotel and room
$hotel = App\Models\Hotel::where('name', 'Hotel Senne')->first();
$room = $hotel->rooms->first();
echo "Hotel: {$hotel->name}\n";
echo "Room: {$room->room_number}\n";
echo 'Cleaners: '.$hotel->cleaners()->count()."\n\n";
// Check capacity
$tomorrow = now()->addDay()->toDateString();
$capacity = App\Models\DayCapacity::where('hotel_id', $hotel->id)
    ->where('date', $tomorrow)
    ->first();
echo "Capacity for $tomorrow: ".($capacity ? $capacity->capacity : 'NOT SET')."\n\n";
// Create booking
$checkIn = now()->addDay()->setTime(15, 0);
$booking = App\Models\Booking::create([
    'room_id' => $room->id,
    'guest_name' => 'Direct Test Guest',
    'check_in' => $checkIn->toDateString(),
    'check_out' => $checkIn->copy()->addDays(2)->toDateString(),
    'check_in_datetime' => $checkIn,
    'check_out_datetime' => $checkIn->copy()->addDays(2),
]);
echo "Booking created: {$booking->id}\n";
// Check for task
$booking->load('cleaningTask');
if ($booking->cleaningTask) {
    echo "✅ Task created! ID: {$booking->cleaningTask->id}\n";
} else {
    echo "❌ No task created\n";
}
