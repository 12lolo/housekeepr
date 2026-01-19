#!/bin/bash

echo "==========================================="
echo "Testing Cleaner Availability Fix"
echo "==========================================="
echo ""

echo "Step 1: Reset database and seed..."
php artisan migrate:fresh --seed
echo ""

echo "Step 2: Check cleaner availability records..."
php artisan tinker --execute="
\$cleaners = App\Models\Cleaner::with('availability')->get();
foreach (\$cleaners as \$cleaner) {
    \$days = \$cleaner->availability->pluck('day_of_week')->toArray();
    \$dayNames = array_map(function(\$d) {
        return ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'][\$d];
    }, \$days);
    echo \"Cleaner #{\$cleaner->id} ({\$cleaner->user->name}): \" . implode(', ', \$dayNames) . \"\n\";
}
"
echo ""

echo "Step 3: Check bookings without tasks..."
php artisan tinker --execute="
\$count = App\Models\Booking::doesntHave('cleaningTask')->count();
echo \"Bookings without tasks: \$count\n\";
"
echo ""

echo "Step 4: Trigger booking events..."
php artisan housekeepr:trigger-booking-events
echo ""

echo "Step 5: Check schedule..."
php artisan housekeepr:debug-schedule
echo ""

echo "Step 6: Show sample cleaning tasks with day info..."
php artisan tinker --execute="
\$tasks = App\Models\CleaningTask::with(['cleaner.user', 'room', 'booking'])->take(10)->get();
foreach (\$tasks as \$task) {
    \$dayName = \Carbon\Carbon::parse(\$task->date)->dayName;
    \$dayOfWeek = \Carbon\Carbon::parse(\$task->date)->dayOfWeek;
    echo \"Task #{\$task->id}: Room {\$task->room->room_number}, {\$task->date} ({\$dayName}/{\$dayOfWeek}), Cleaner: {\$task->cleaner->user->name}\n\";
}
"
echo ""

echo "==========================================="
echo "✅ Test Complete!"
echo "==========================================="

