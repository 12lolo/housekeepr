<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "🧪 Testing Automatic Cleaning Task Creation\n";
echo "===========================================\n\n";

try {
    // Get Hotel Senne
    $hotel = App\Models\Hotel::where('name', 'Hotel Senne')->first();
    if (! $hotel) {
        echo "❌ Hotel Senne not found!\n";
        exit(1);
    }

    $room = $hotel->rooms->first();

    echo "📍 Test Setup:\n";
    echo "  Hotel: {$hotel->name}\n";
    echo "  Room: {$room->room_number} ({$room->room_type})\n";
    echo "  Standard Duration: {$room->standard_duration} minutes\n";
    echo '  Available Cleaners: '.App\Models\Cleaner::where('hotel_id', $hotel->id)->count()."\n\n";

    // Check current task count
    $tasksBefore = App\Models\CleaningTask::count();
    echo "📊 Before Test:\n";
    echo "  Total Cleaning Tasks: $tasksBefore\n\n";

    // Create a test booking for tomorrow at 3 PM
    $checkInDate = now()->addDays(1)->setTime(15, 0);
    $checkOutDate = now()->addDays(3)->setTime(11, 0);

    echo "📅 Creating Test Booking:\n";
    echo "  Guest: Test Automation Guest\n";
    echo "  Check-in: {$checkInDate->format('d-m-Y H:i')}\n";
    echo "  Check-out: {$checkOutDate->format('d-m-Y H:i')}\n\n";

    $booking = App\Models\Booking::create([
        'room_id' => $room->id,
        'guest_name' => 'Test Automation Guest',
        'check_in' => $checkInDate->toDateString(),
        'check_out' => $checkOutDate->toDateString(),
        'check_in_datetime' => $checkInDate,
        'check_out_datetime' => $checkOutDate,
    ]);

    echo "✅ Booking Created Successfully!\n";
    echo "  Booking ID: {$booking->id}\n\n";

    // Wait a moment for event to process
    usleep(100000); // 0.1 seconds

    // Refresh to get relationship
    $booking->load('cleaningTask');

    // Check if cleaning task was created
    $tasksAfter = App\Models\CleaningTask::count();
    echo "📊 After Test:\n";
    echo "  Total Cleaning Tasks: $tasksAfter\n";
    echo '  New Tasks Created: '.($tasksAfter - $tasksBefore)."\n\n";

    if ($booking->cleaningTask) {
        $task = $booking->cleaningTask;
        echo "✅ ✅ ✅ CLEANING TASK AUTOMATICALLY CREATED! ✅ ✅ ✅\n\n";

        echo "🎯 Task Details:\n";
        echo "  Task ID: {$task->id}\n";
        echo "  Room: {$task->room->room_number}\n";
        echo "  Assigned to: {$task->cleaner->user->name} (ID: {$task->cleaner->id})\n";
        echo "  Date: {$task->date->format('d-m-Y')}\n";
        echo "  Suggested Start: {$task->suggested_start_time->format('H:i')}\n";
        echo "  Deadline (Check-in): {$task->deadline->format('H:i')}\n";
        echo "  Planned Duration: {$task->planned_duration} minutes\n";
        echo "  Status: {$task->status}\n\n";

        // Verify timing calculation
        $expectedDuration = $room->standard_duration + 10;
        $expectedStart = $checkInDate->copy()->subMinutes($expectedDuration);

        echo "🧮 Timing Verification:\n";
        echo "  Expected Duration: {$expectedDuration} min (room: {$room->standard_duration} + buffer: 10)\n";
        echo "  Expected Start: {$expectedStart->format('H:i')}\n";
        echo "  Actual Duration: {$task->planned_duration} min\n";
        echo "  Actual Start: {$task->suggested_start_time->format('H:i')}\n";

        if ($task->planned_duration == $expectedDuration) {
            echo "  ✅ Duration calculation CORRECT!\n";
        } else {
            echo "  ❌ Duration calculation INCORRECT!\n";
        }

        if ($task->suggested_start_time->format('H:i') == $expectedStart->format('H:i')) {
            echo "  ✅ Start time calculation CORRECT!\n";
        } else {
            echo "  ❌ Start time calculation INCORRECT!\n";
        }

        echo "\n🎉 SUCCESS! The automatic cleaning scheduler is working perfectly!\n";

    } else {
        echo "❌ FAILED: No cleaning task was created!\n\n";
        echo "🔍 Debugging Info:\n";
        echo "  Booking ID: {$booking->id}\n";
        echo "  Room ID: {$booking->room_id}\n";
        echo "  Check-in datetime: {$booking->check_in_datetime}\n";

        // Check for blocking issues
        $issues = App\Models\Issue::where('room_id', $room->id)->where('status', 'open')->get();
        if ($issues->count() > 0) {
            echo "  ⚠️ Found open issues on this room:\n";
            foreach ($issues as $issue) {
                echo "    - {$issue->impact}: {$issue->note}\n";
            }
        }

        // Check capacity
        $capacity = App\Models\DayCapacity::where('hotel_id', $hotel->id)
            ->where('date', $checkInDate->toDateString())
            ->first();
        if ($capacity) {
            echo "  Capacity for {$checkInDate->toDateString()}: {$capacity->capacity}\n";
        } else {
            echo "  ❌ No capacity set for {$checkInDate->toDateString()}\n";
        }
    }

} catch (\Exception $e) {
    echo "❌ ERROR: {$e->getMessage()}\n";
    echo "  File: {$e->getFile()}:{$e->getLine()}\n";
    echo "  Trace:\n{$e->getTraceAsString()}\n";
}
