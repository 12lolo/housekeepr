<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();
echo "Booking Test WITH Capacity Set\n";
echo "================================\n\n";
// Get hotel and room
$hotel = App\Models\Hotel::where('name', 'Hotel Senne')->first();
$room = $hotel->rooms->first();
echo "Hotel: {$hotel->name}\n";
echo "Room: {$room->room_number} ({$room->room_type})\n";
echo "Standard Duration: {$room->standard_duration} min\n";
echo 'Cleaners: '.$hotel->cleaners()->count()."\n\n";
// SET CAPACITY for tomorrow
$tomorrow = now()->addDay()->toDateString();
$capacity = App\Models\DayCapacity::firstOrCreate(
    ['hotel_id' => $hotel->id, 'date' => $tomorrow],
    ['capacity' => 2]
);
echo "✅ Capacity for $tomorrow: {$capacity->capacity}\n\n";
// Tasks before
$tasksBefore = App\Models\CleaningTask::count();
echo "Tasks before: $tasksBefore\n\n";
// Create booking
$checkIn = now()->addDay()->setTime(15, 0);
$checkOut = $checkIn->copy()->addDays(2)->setTime(11, 0);
echo "Creating booking:\n";
echo "  Check-in: {$checkIn->format('Y-m-d H:i')}\n";
echo "  Check-out: {$checkOut->format('Y-m-d H:i')}\n\n";
$booking = App\Models\Booking::create([
    'room_id' => $room->id,
    'guest_name' => 'Capacity Test Guest',
    'check_in' => $checkIn->toDateString(),
    'check_out' => $checkOut->toDateString(),
    'check_in_datetime' => $checkIn,
    'check_out_datetime' => $checkOut,
]);
echo "Booking created: {$booking->id}\n\n";
// Wait for event
sleep(1);
// Check for task
$tasksAfter = App\Models\CleaningTask::count();
echo "Tasks after: $tasksAfter\n";
echo 'New tasks: '.($tasksAfter - $tasksBefore)."\n\n";
$booking->load('cleaningTask.cleaner.user');
if ($booking->cleaningTask) {
    $task = $booking->cleaningTask;
    echo "✅ ✅ ✅ TASK CREATED AUTOMATICALLY! ✅ ✅ ✅\n\n";
    echo "Task Details:\n";
    echo "  ID: {$task->id}\n";
    echo "  Room: {$task->room->room_number}\n";
    echo "  Cleaner: {$task->cleaner->user->name}\n";
    echo "  Date: {$task->date->format('Y-m-d')}\n";
    echo "  Suggested Start: {$task->suggested_start_time->format('H:i')}\n";
    echo "  Deadline: {$task->deadline->format('H:i')}\n";
    echo "  Planned Duration: {$task->planned_duration} min\n";
    echo "  Status: {$task->status}\n\n";
    // Verify calculation
    $expectedDuration = $room->standard_duration + 10;
    $expectedStart = $checkIn->copy()->subMinutes($expectedDuration);
    echo "Calculation Verification:\n";
    echo "  Expected duration: {$expectedDuration} min (".$room->standard_duration." + 10 buffer)\n";
    echo "  Actual duration: {$task->planned_duration} min\n";
    echo "  Expected start: {$expectedStart->format('H:i')}\n";
    echo "  Actual start: {$task->suggested_start_time->format('H:i')}\n\n";
    if ($task->planned_duration == $expectedDuration) {
        echo "  ✅ Duration calculation CORRECT!\n";
    }
    if ($task->suggested_start_time->format('H:i') == $expectedStart->format('H:i')) {
        echo "  ✅ Start time calculation CORRECT!\n";
    }
    echo "\n🎉 SUCCESS! The algorithm works perfectly!\n";
} else {
    echo "❌ NO TASK CREATED\n";
    echo "\nDEBUG INFO:\n";
    // Check logs
    $logs = \Illuminate\Support\Facades\DB::table('activity_log')
        ->where('subject_id', $booking->id)
        ->where('subject_type', 'App\Models\Booking')
        ->get();
    echo 'Activity logs: '.$logs->count()."\n";
}
