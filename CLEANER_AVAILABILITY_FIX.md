# Cleaner Availability Fix

## Problem
The cleaning scheduler was not creating tasks because cleaners had no availability records in the `cleaner_availability` table. The scheduler correctly checks for cleaner availability by day of week, but all cleaners had 0 availability records.

## Solution

### 1. Updated DatabaseSeeder
Added cleaner availability records with realistic schedules:

**Hotel 1 (Amsterdam Central):**
- Maria Janssen: Monday-Friday (1-5)
- Jan de Vries: Monday-Friday (1-5)
- Anna Bakker: Tuesday-Saturday (2-6)
- Peter Smit: Monday, Wednesday, Friday (1,3,5) - part-time

**Hotel 2 (Rotterdam):**
- Lisa van Dam: All days (0-6) - full coverage

**Hotel 3 (Senne's Hotel):**
- Emma de Groot: Monday-Friday (1-5)
- Tom Hendriks: Thursday-Monday (0,1,4,5,6) - weekend coverage

### 2. Created Migration Command
Created `PopulateCleanerAvailability` command to set Monday-Friday availability for existing production cleaners.

## Production Fix Steps

Run these commands in production:

```bash
# Step 1: Populate availability for existing cleaners
php artisan housekeepr:populate-cleaner-availability

# Step 2: Verify cleaners have availability
php artisan tinker --execute="echo 'Cleaners with availability: ' . App\Models\Cleaner::has('availability')->count() . ' / ' . App\Models\Cleaner::count();"

# Step 3: Trigger booking events again to create cleaning tasks
php artisan housekeepr:trigger-booking-events

# Step 4: Verify tasks were created
php artisan housekeepr:debug-schedule
```

## How the Scheduler Works

1. When a booking is created/updated, `CreateCleaningTaskForBooking` listener fires
2. Calculates the cleaning date from check-in datetime
3. Gets day of week (0=Sunday, 1=Monday, ..., 6=Saturday)
4. Queries for active cleaners who have availability on that day:
   ```php
   $cleaners = $hotel->cleaners()
       ->where('status', 'active')
       ->whereHas('availability', function ($query) use ($dayOfWeek) {
           $query->where('day_of_week', $dayOfWeek);
       })
       ->get();
   ```
5. If no cleaners available, creates urgent issue
6. Otherwise, assigns to cleaner with fewest tasks that day (load balancing)

## Database Schema

```sql
-- cleaner_availability table structure
CREATE TABLE cleaner_availability (
    id BIGINT PRIMARY KEY,
    cleaner_id BIGINT,  -- Foreign key to cleaners table
    day_of_week TINYINT, -- 0=Sunday, 1=Monday, ..., 6=Saturday
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE(cleaner_id, day_of_week)
);
```

## Future Enhancements

Consider adding to the cleaner profile UI:
- Checkbox selection for available days
- View/edit availability schedule
- Holiday/time-off management
- Shift time preferences (morning/afternoon/evening)

