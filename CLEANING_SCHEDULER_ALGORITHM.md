# Cleaning Scheduler Algorithm

## Overview

Yes, **HouseKeepr already has a comprehensive automatic cleaning scheduling algorithm** that plans cleaners based on bookings and ensures rooms are cleaned before new guests check in.

## How It Works

### 1. **Automatic Task Creation (Event-Driven)**

When a booking is created or updated, the system automatically:

#### Event Flow:
```
Booking Created/Updated
    ↓
BookingCreated/BookingUpdated Event Fired
    ↓
CreateCleaningTaskForBooking Listener Triggered
    ↓
Cleaning Task Automatically Created & Assigned
```

#### Logic in `CreateCleaningTaskForBooking.php`:

1. **Checks if task already exists** - Prevents duplicates
2. **Checks for blocking issues** - If room has "kan_niet_gebruikt" issue, skip
3. **Calculates timing**:
   - `planned_duration` = room's `standard_duration` + 10 min buffer
   - `suggested_start_time` = check-in time - planned_duration
   - `deadline` = check-in datetime
4. **Validates capacity** - Checks if cleaners are available that day
5. **Assigns cleaner automatically** using **load balancing**:
   - Sorts cleaners by number of tasks on that date
   - Assigns to cleaner with fewest tasks
6. **Creates urgent issues** if:
   - No capacity set for the day
   - No active cleaners available
   - Not enough time before check-in

### 2. **Manual Planning Command (Batch Processing)**

For bulk planning or replanning, there's a console command:

```bash
php artisan hcs:plan-tasks [options]
```

#### Options:
- `--hotel=X` - Plan for specific hotel
- `--date=YYYY-MM-DD` - Plan for specific date
- `--days=7` - Plan X days ahead (default: 7)
- `--force` - Force replanning of existing tasks

#### Features:
- Processes multiple hotels at once
- Plans 7+ days ahead
- Load balances across cleaners
- Handles blocking issues
- Creates urgent issues when needed
- Sends email notifications to owners

## Key Algorithm Components

### Load Balancing Algorithm

```php
$assignedCleaner = $cleaners->sortBy(function ($cleaner) use ($date) {
    return $cleaner->cleaningTasks()
        ->where('date', $date)
        ->count();
})->first();
```

**Result**: Cleaner with fewest tasks on that date gets assigned.

### Timing Calculation

```php
// Example: Room check-in at 16:00, standard duration 60 min
$plannedDuration = 60 + 10; // 70 minutes total
$suggestedStartTime = 16:00 - 70 min = 14:50
$deadline = 16:00
```

This ensures:
- ✅ Cleaner has enough time (60 min standard + 10 min buffer)
- ✅ Room is ready before guest arrival
- ✅ Buffer for unexpected delays

### Blocking Issue Detection

If a room has an open issue with impact "kan_niet_gebruikt":
- ❌ No cleaning task is created
- ⚠️ Warning logged
- 📧 Owner notified (if urgent)

### Capacity Management

System checks `day_capacities` table:
- If capacity = 0 → No cleaners available
- If capacity = 2 → Max 2 cleaning tasks that day
- Missing capacity → Creates urgent issue

## Data Structure

### CleaningTask Model Fields:
- `room_id` - Which room to clean
- `cleaner_id` - Assigned cleaner
- `booking_id` - Related booking
- `date` - Date to clean
- `deadline` - Must be done by (check-in time)
- `planned_duration` - Expected time needed
- `suggested_start_time` - When to start
- `status` - pending/in_progress/completed
- `actual_start_time` - When cleaner actually started
- `actual_end_time` - When cleaner finished
- `actual_duration` - How long it actually took

## Safety Features

### 1. Urgent Issue Creation
When problems detected:
- ✉️ Email sent to hotel owner
- 🚨 Issue created in system
- 📝 Logged for tracking

### 2. Time Validation
```php
if ($suggestedStartTime->lessThan(now())) {
    Log::warning("Not enough time for cleaning");
    // Still creates task but flags as urgent
}
```

### 3. Duplicate Prevention
```php
if ($booking->cleaningTask()->exists()) {
    return; // Skip if task already exists
}
```

## Usage Examples

### Automatic (Default Behavior)
```php
// In your booking controller:
$booking = Booking::create([...]);
// ✅ Cleaning task automatically created via event
```

### Manual Planning
```bash
# Plan next 7 days for all hotels
php artisan hcs:plan-tasks

# Plan specific hotel for next 14 days
php artisan hcs:plan-tasks --hotel=3 --days=14

# Force replan all tasks for tomorrow
php artisan hcs:plan-tasks --date=2025-12-16 --force
```

## Integration Points

### 1. Event Listeners (AppServiceProvider.php)
```php
Event::listen(
    \App\Events\BookingCreated::class,
    \App\Listeners\CreateCleaningTaskForBooking::class
);
```

### 2. Booking Model (Automatic)
```php
protected static function booted()
{
    static::created(function ($booking) {
        event(new \App\Events\BookingCreated($booking));
    });
}
```

### 3. Manual via Command
```bash
php artisan hcs:plan-tasks
```

## Statistics & Monitoring

The planning command provides stats:
```
📊 Planning Statistics
======================
✅ Tasks planned: 15
🔄 Tasks replanned: 3
🚫 Tasks blocked: 2
❌ Errors: 1
```

## Best Practices

1. **Set day capacities in advance** - Ensures cleaners can be assigned
2. **Run planning command daily** - Catch any missed bookings
3. **Monitor urgent issues** - Review system-generated issues
4. **Use `--force` carefully** - Only when replanning is necessary
5. **Keep room standard_duration updated** - Affects all calculations

## Future Enhancements (Potential)

- [ ] Smart scheduling based on cleaner preferences
- [ ] Route optimization (clean nearby rooms together)
- [ ] Cleaner skill matching (deluxe rooms → experienced cleaners)
- [ ] Predictive duration based on historical data
- [ ] Real-time availability checking
- [ ] Mobile push notifications for cleaners

## Summary

✅ **Yes, the algorithm exists and is fully functional!**

It automatically:
- Creates cleaning tasks when bookings are made
- Assigns cleaners using load balancing
- Calculates optimal timing
- Handles edge cases (no capacity, blocking issues)
- Sends notifications when problems occur
- Can batch plan/replan via command

The system ensures rooms are always cleaned before new guests arrive by calculating the exact time needed and assigning the task to the cleaner with the lightest workload that day.

