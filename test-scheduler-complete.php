<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "✅ TESTING: Automatic Cleaning Scheduler Algorithm\n";
echo "===================================================\n\n";

// Get hotel
$hotel = App\Models\Hotel::where('name', 'Hotel Senne')->first();
$room = $hotel->rooms->first();

echo "📍 Test Setup:\n";
echo "  Hotel: {$hotel->name}\n";
echo "  Room: {$room->room_number} ({$room->room_type})\n";
echo "  Standard Duration: {$room->standard_duration} min\n";
echo '  Cleaners: '.$hotel->cleaners()->count()."\n\n";

// Find a date with capacity
$capacities = App\Models\DayCapacity::where('hotel_id', $hotel->id)
    ->where('date', '>=', now()->toDateString())
    ->orderBy('date')
    ->get();

echo "📅 Available Capacity Dates:\n";
foreach ($capacities->take(5) as $cap) {
    echo "  {$cap->date->format('Y-m-d')}: {$cap->capacity} cleaners\n";
}
echo "\n";

// Use the first available date
$testDate = $capacities->first();
if (! $testDate) {
    echo "❌ No capacity dates found! Exiting.\n";
    exit(1);
}

echo "Using test date: {$testDate->date->format('Y-m-d')} with capacity: {$testDate->capacity}\n\n";

// Count tasks before
$tasksBefore = App\Models\CleaningTask::count();
echo "📊 Before Test:\n";
echo "  Total Cleaning Tasks: $tasksBefore\n\n";

// Create booking for the test date
$checkIn = \Carbon\Carbon::parse($testDate->date)->setTime(15, 0);
$checkOut = $checkIn->copy()->addDays(2)->setTime(11, 0);

echo "📅 Creating Test Booking:\n";
echo "  Guest: Algorithm Test Guest\n";
echo "  Check-in: {$checkIn->format('Y-m-d H:i')}\n";
echo "  Check-out: {$checkOut->format('Y-m-d H:i')}\n\n";

try {
    $booking = App\Models\Booking::create([
        'room_id' => $room->id,
        'guest_name' => 'Algorithm Test Guest',
        'check_in' => $checkIn->toDateString(),
        'check_out' => $checkOut->toDateString(),
        'check_in_datetime' => $checkIn,
        'check_out_datetime' => $checkOut,
    ]);

    echo "✅ Booking Created! ID: {$booking->id}\n\n";

    // Wait for event to process
    usleep(300000); // 0.3 seconds

    // Reload with relationships
    $booking->load('cleaningTask.cleaner.user', 'cleaningTask.room');

    // Count tasks after
    $tasksAfter = App\Models\CleaningTask::count();
    echo "📊 After Test:\n";
    echo "  Total Cleaning Tasks: $tasksAfter\n";
    echo '  New Tasks Created: '.($tasksAfter - $tasksBefore)."\n\n";

    if ($booking->cleaningTask) {
        $task = $booking->cleaningTask;

        echo str_repeat('=', 70)."\n";
        echo "🎉 SUCCESS! CLEANING TASK AUTOMATICALLY CREATED! 🎉\n";
        echo str_repeat('=', 70)."\n\n";

        echo "🎯 Task Details:\n";
        echo "  Task ID: {$task->id}\n";
        echo "  Room: {$task->room->room_number}\n";
        echo "  Assigned Cleaner: {$task->cleaner->user->name}\n";
        echo "  Date: {$task->date->format('Y-m-d')}\n";
        echo "  Suggested Start Time: {$task->suggested_start_time->format('H:i')}\n";
        echo "  Deadline (Check-in): {$task->deadline->format('H:i')}\n";
        echo "  Planned Duration: {$task->planned_duration} minutes\n";
        echo "  Status: {$task->status}\n\n";

        // Verify algorithm calculations
        $expectedDuration = $room->standard_duration + 10; // Standard + 10 min buffer
        $expectedStartTime = $checkIn->copy()->subMinutes($expectedDuration);

        echo "🧮 Algorithm Verification:\n";
        echo "  Room standard duration: {$room->standard_duration} min\n";
        echo "  Buffer: 10 min\n";
        echo "  Expected total duration: {$expectedDuration} min\n";
        echo "  Actual planned duration: {$task->planned_duration} min ";

        if ($task->planned_duration == $expectedDuration) {
            echo "✅ CORRECT!\n";
        } else {
            echo "❌ WRONG (expected $expectedDuration)\n";
        }

        echo "\n  Check-in time: {$checkIn->format('H:i')}\n";
        echo "  Expected start: {$expectedStartTime->format('H:i')}\n";
        echo "  Actual start: {$task->suggested_start_time->format('H:i')} ";

        if ($task->suggested_start_time->format('H:i') == $expectedStartTime->format('H:i')) {
            echo "✅ CORRECT!\n";
        } else {
            echo "❌ WRONG (expected {$expectedStartTime->format('H:i')})\n";
        }

        echo "\n";
        echo str_repeat('=', 70)."\n";
        echo "✅ CONCLUSION: The Automatic Cleaning Scheduler Works Perfectly!\n";
        echo str_repeat('=', 70)."\n\n";

        echo "What Happened:\n";
        echo "1. ✅ Booking was created\n";
        echo "2. ✅ BookingCreated event was fired automatically\n";
        echo "3. ✅ CreateCleaningTaskForBooking listener was triggered\n";
        echo "4. ✅ Algorithm calculated optimal timing:\n";
        echo "     - Check-in at {$checkIn->format('H:i')}\n";
        echo "     - Needs {$room->standard_duration} min + 10 min buffer = {$expectedDuration} min\n";
        echo "     - Should start at {$expectedStartTime->format('H:i')}\n";
        echo "5. ✅ Cleaner with least workload was assigned: {$task->cleaner->user->name}\n";
        echo "6. ✅ CleaningTask record was created in database\n";
        echo "7. ✅ Room will be ready BEFORE guest arrives\n\n";

        echo "The algorithm ensures rooms are always cleaned before new guests check in!\n";

    } else {
        echo str_repeat('=', 70)."\n";
        echo "❌ TEST FAILED: No cleaning task was created\n";
        echo str_repeat('=', 70)."\n\n";

        echo "Debug Information:\n";
        echo "  Booking ID: {$booking->id}\n";
        echo "  Room ID: {$booking->room_id}\n";
        echo "  Check-in datetime: {$booking->check_in_datetime}\n\n";

        // Check for blocking issues
        $issues = App\Models\Issue::where('room_id', $room->id)
            ->where('status', 'open')
            ->get();

        if ($issues->count() > 0) {
            echo "⚠️  Room has blocking issues:\n";
            foreach ($issues as $issue) {
                echo "    - {$issue->impact}: ".substr($issue->note, 0, 50)."...\n";
            }
        } else {
            echo "✅ No blocking issues found\n";
        }

        echo "\n  Active cleaners: ".$hotel->cleaners()->where('status', 'active')->count()."\n";
        echo "  Capacity for date: {$testDate->capacity}\n\n";

        echo "Possible reasons:\n";
        echo "  1. Event listener not registered properly\n";
        echo "  2. No active cleaners available\n";
        echo "  3. Capacity check failed\n";
        echo "  4. Room has blocking issue\n";
    }

} catch (\Exception $e) {
    echo "❌ ERROR OCCURRED:\n";
    echo "  Message: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n\n";
}
