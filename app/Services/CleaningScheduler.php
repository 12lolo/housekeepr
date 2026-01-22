<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\CleaningTask;
use App\Models\Hotel;
use App\Models\Issue;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class CleaningScheduler
{
    protected const TRAVEL_TIME_MINUTES = 5; // Time to move between rooms

    /**
     * Schedule cleaning tasks for a specific hotel and date.
     * Uses intelligent time-based scheduling with conflict detection.
     */
    public function scheduleForDate(Hotel $hotel, Carbon $date): array
    {
        $stats = [
            'scheduled' => 0,
            'conflicts' => 0,
            'no_cleaner' => 0,
            'impossible' => 0,
        ];

        // Get all bookings that need cleaning on this date
        $bookings = Booking::whereHas('room', function ($query) use ($hotel) {
            $query->where('hotel_id', $hotel->id);
        })
            ->whereDate('check_in_datetime', $date)
            ->with(['room', 'cleaningTask'])
            ->get();

        if ($bookings->isEmpty()) {
            return $stats;
        }

        // Get available cleaners for this day of week
        $dayOfWeek = $date->dayOfWeek;
        $dayColumn = $this->getDayColumn($dayOfWeek);

        $availableCleaners = $hotel->cleaners()
            ->where('status', 'active')
            ->where($dayColumn, true)
            ->get();

        if ($availableCleaners->isEmpty()) {
            // No cleaners available - create urgent issue for each booking
            foreach ($bookings as $booking) {
                $this->createNoCleanerIssue($booking, $date);
                $stats['no_cleaner']++;
            }

            return $stats;
        }

        // Prepare tasks with time windows and detect impossible schedules
        $tasksToSchedule = [];
        foreach ($bookings as $booking) {
            $taskData = $this->prepareTask($booking, $date);

            if ($taskData === null) {
                // Task already exists and is not pending - skip
                continue;
            }

            if ($taskData['impossible']) {
                // Not enough time to clean - create specific issue
                $this->createImpossibleScheduleIssue($booking, $taskData);
                $stats['impossible']++;

                continue;
            }

            $tasksToSchedule[] = $taskData;
        }

        if (empty($tasksToSchedule)) {
            return $stats;
        }

        // Sort by earliest available time (checkout time), then by duration (longest first for better packing)
        usort($tasksToSchedule, function ($a, $b) {
            $timeCompare = $a['available_from'] <=> $b['available_from'];
            if ($timeCompare !== 0) {
                return $timeCompare;
            }

            return $b['duration'] <=> $a['duration']; // Longest first
        });

        // Initialize cleaner schedules with existing pending tasks
        $cleanerSchedules = $this->initializeCleanerSchedules($availableCleaners, $hotel, $date);

        // Sort cleaners by current workload (fewest tasks first for better balancing)
        uasort($cleanerSchedules, function ($a, $b) {
            return count($a['tasks']) <=> count($b['tasks']);
        });

        // Assign tasks using improved greedy algorithm with workload balancing
        foreach ($tasksToSchedule as $taskData) {
            $assigned = false;

            // Try to assign to each cleaner (sorted by workload)
            foreach ($cleanerSchedules as $cleanerId => $schedule) {
                if ($this->canAssignTask($schedule['tasks'], $taskData)) {
                    $cleanerSchedules[$cleanerId]['tasks'][] = $taskData;
                    $assigned = true;

                    // Re-sort cleaners after assignment to maintain balance
                    uasort($cleanerSchedules, function ($a, $b) {
                        return count($a['tasks']) <=> count($b['tasks']);
                    });

                    break;
                }
            }

            if (! $assigned) {
                // Couldn't assign - create detailed conflict issue
                $conflictReason = $this->findConflictReason($cleanerSchedules, $taskData);
                $this->createSchedulingConflictIssue($taskData['booking'], $date, $conflictReason);
                $stats['conflicts']++;
            } else {
                $stats['scheduled']++;
            }
        }

        // Create/update cleaning tasks in database
        foreach ($cleanerSchedules as $cleanerId => $schedule) {
            foreach ($schedule['tasks'] as $taskData) {
                // Skip tasks that were already in the schedule (existing pending tasks)
                if (! isset($taskData['is_new']) || $taskData['is_new']) {
                    $this->createOrUpdateTask($taskData, $cleanerId);
                }
            }
        }

        return $stats;
    }

    /**
     * Initialize cleaner schedules with existing pending tasks.
     */
    protected function initializeCleanerSchedules($cleaners, Hotel $hotel, Carbon $date): array
    {
        $schedules = [];

        foreach ($cleaners as $cleaner) {
            // Get existing pending tasks for this cleaner on this date
            $existingTasks = CleaningTask::where('cleaner_id', $cleaner->id)
                ->whereDate('date', $date)
                ->where('status', 'pending') // Only pending tasks can be rescheduled
                ->with(['room', 'booking'])
                ->get();

            $tasks = [];
            foreach ($existingTasks as $task) {
                // Add existing tasks to schedule (with travel time)
                $tasks[] = [
                    'booking' => $task->booking,
                    'room' => $task->room,
                    'available_from' => Carbon::parse($task->suggested_start_time),
                    'suggested_start' => Carbon::parse($task->suggested_start_time),
                    'duration' => $task->planned_duration,
                    'end_time' => Carbon::parse($task->suggested_start_time)->addMinutes($task->planned_duration + self::TRAVEL_TIME_MINUTES),
                    'deadline' => Carbon::parse($task->booking->check_in_datetime),
                    'date' => $date->toDateString(),
                    'is_new' => false, // Mark as existing
                ];
            }

            $schedules[$cleaner->id] = [
                'cleaner' => $cleaner,
                'tasks' => $tasks,
            ];
        }

        return $schedules;
    }

    /**
     * Prepare single task data from booking.
     * Returns null if task should be skipped (already exists and not pending).
     */
    protected function prepareTask(Booking $booking, Carbon $date): ?array
    {
        $room = $booking->room;

        // Skip if room has blocking issue
        if ($this->hasBlockingIssue($room->id)) {
            return null;
        }

        // Skip if task exists and is not pending (completed or in-progress)
        $existingTask = $booking->cleaningTask;
        if ($existingTask && $existingTask->status !== 'pending') {
            return null;
        }

        // Calculate time window
        $checkoutTime = Carbon::parse($date->toDateString().' '.$room->checkout_time);
        $checkinTime = Carbon::parse($booking->check_in_datetime);
        $cleaningDuration = $room->standard_duration + 10; // Add 10 min buffer
        $totalDuration = $cleaningDuration + self::TRAVEL_TIME_MINUTES; // Add travel time

        // Calculate available time window
        $availableMinutes = $checkoutTime->diffInMinutes($checkinTime);

        // Check if schedule is impossible
        if ($totalDuration > $availableMinutes) {
            return [
                'booking' => $booking,
                'room' => $room,
                'impossible' => true,
                'required_minutes' => $totalDuration,
                'available_minutes' => $availableMinutes,
                'checkout_time' => $checkoutTime,
                'checkin_time' => $checkinTime,
            ];
        }

        // Suggested start: As soon as room is available (after checkout)
        $suggestedStart = $checkoutTime;

        // Deadline: Must be done before check-in (actual deadline)
        $deadline = $checkinTime;

        // End time: Start + cleaning duration + travel time
        $endTime = $suggestedStart->copy()->addMinutes($totalDuration);

        // Check if there's enough time
        if ($endTime->greaterThan($deadline)) {
            // Not enough time - adjust start time backwards
            $suggestedStart = $deadline->copy()->subMinutes($totalDuration);

            // If start time is before checkout, start ASAP
            if ($suggestedStart->lessThan($checkoutTime)) {
                $suggestedStart = $checkoutTime;
                $endTime = $checkoutTime->copy()->addMinutes($totalDuration);
            } else {
                $endTime = $suggestedStart->copy()->addMinutes($totalDuration);
            }
        }

        return [
            'booking' => $booking,
            'room' => $room,
            'available_from' => $checkoutTime,
            'suggested_start' => $suggestedStart,
            'duration' => $cleaningDuration, // Just cleaning time for database
            'total_duration' => $totalDuration, // Cleaning + travel for scheduling
            'end_time' => $endTime,
            'deadline' => $deadline,
            'date' => $date->toDateString(),
            'impossible' => false,
            'is_new' => true,
        ];
    }

    /**
     * Check if a task can be assigned to a cleaner without time conflicts.
     */
    protected function canAssignTask(array $assignedTasks, array $newTask): bool
    {
        $newStart = $newTask['suggested_start'];
        $newEnd = $newTask['end_time'];

        foreach ($assignedTasks as $existing) {
            $existingStart = $existing['suggested_start'];
            $existingEnd = $existing['end_time'];

            // Check for overlap
            if ($this->tasksOverlap($newStart, $newEnd, $existingStart, $existingEnd)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Check if two time periods overlap.
     */
    protected function tasksOverlap(Carbon $start1, Carbon $end1, Carbon $start2, Carbon $end2): bool
    {
        // Tasks overlap if one starts before the other ends
        return $start1->lessThan($end2) && $end1->greaterThan($start2);
    }

    /**
     * Create or update cleaning task in database.
     */
    protected function createOrUpdateTask(array $taskData, int $cleanerId): void
    {
        $booking = $taskData['booking'];
        $existingTask = $booking->cleaningTask;

        $data = [
            'room_id' => $taskData['room']->id,
            'cleaner_id' => $cleanerId,
            'date' => $taskData['date'],
            'planned_duration' => $taskData['duration'],
            'suggested_start_time' => $taskData['suggested_start'],
            'deadline' => $taskData['deadline'], // Actual check-in deadline
        ];

        if ($existingTask) {
            // Only update if pending
            if ($existingTask->status === 'pending') {
                $existingTask->update($data);
                Log::info("CleaningScheduler: Updated task #{$existingTask->id} for booking #{$booking->id}, room {$taskData['room']->room_number}, cleaner #{$cleanerId}, start {$taskData['suggested_start']->format('H:i')}");
            } else {
                Log::info("CleaningScheduler: Skipped updating task #{$existingTask->id} for booking #{$booking->id} - status is {$existingTask->status}");
            }
        } else {
            $task = CleaningTask::create(array_merge($data, [
                'booking_id' => $booking->id,
                'status' => 'pending',
            ]));
            Log::info("CleaningScheduler: Created task #{$task->id} for booking #{$booking->id}, room {$taskData['room']->room_number}, cleaner #{$cleanerId}, start {$taskData['suggested_start']->format('H:i')}, duration {$taskData['duration']}min");
        }
    }

    /**
     * Find the specific reason why a task couldn't be scheduled.
     */
    protected function findConflictReason(array $cleanerSchedules, array $taskData): string
    {
        $reasons = [];

        foreach ($cleanerSchedules as $cleanerId => $schedule) {
            $cleanerName = $schedule['cleaner']->user->name;
            $conflictingTasks = [];

            foreach ($schedule['tasks'] as $existing) {
                if ($this->tasksOverlap($taskData['suggested_start'], $taskData['end_time'], $existing['suggested_start'], $existing['end_time'])) {
                    $conflictingTasks[] = "Kamer {$existing['room']->room_number} ({$existing['suggested_start']->format('H:i')}-{$existing['end_time']->format('H:i')})";
                }
            }

            if (! empty($conflictingTasks)) {
                $reasons[] = "{$cleanerName}: Bezet met ".implode(', ', $conflictingTasks);
            }
        }

        if (empty($reasons)) {
            return 'Alle schoonmakers zijn volledig bezet op dit tijdstip.';
        }

        return implode("\n", $reasons);
    }

    /**
     * Create issue for impossible schedule (not enough time).
     */
    protected function createImpossibleScheduleIssue(Booking $booking, array $taskData): void
    {
        $room = $taskData['room'];
        $required = $taskData['required_minutes'];
        $available = $taskData['available_minutes'];
        $shortage = $required - $available;

        $checkoutTime = $taskData['checkout_time']->format('H:i');
        $checkinTime = $taskData['checkin_time']->format('H:i');

        // Check if identical issue already exists (deduplicate)
        $existingIssue = Issue::where('room_id', $room->id)
            ->where('status', 'open')
            ->where('note', 'like', '%Boeking #'.$booking->id.'%')
            ->where('note', 'like', '%Onvoldoende tijd voor schoonmaak%')
            ->first();

        if ($existingIssue) {
            return; // Don't create duplicate
        }

        Issue::create([
            'room_id' => $room->id,
            'reported_by' => \App\Models\User::getSystemUserId(),
            'impact' => 'graag_snel',
            'note' => "URGENT: Onvoldoende tijd voor schoonmaak\n\n"
                ."Boeking #{$booking->id}\n"
                ."Kamer: {$room->room_number}\n"
                ."Check-out: {$checkoutTime}\n"
                ."Check-in: {$checkinTime}\n\n"
                ."Beschikbare tijd: {$available} minuten\n"
                ."Benodigde tijd: {$required} minuten (inclusief 5 min reistijd)\n"
                ."Tekort: {$shortage} minuten\n\n"
                .'Oplossing: Verplaats de check-in tijd of verkort de schoonmaaktijd.',
            'status' => 'open',
        ]);

        Log::warning("Impossible schedule for booking {$booking->id}: needs {$required} min, only {$available} min available");
    }

    /**
     * Check if room has blocking issue.
     */
    protected function hasBlockingIssue(int $roomId): bool
    {
        return Issue::where('room_id', $roomId)
            ->where('status', 'open')
            ->where('impact', 'kan_niet_gebruikt')
            ->exists();
    }

    /**
     * Create issue when no cleaners are available.
     */
    protected function createNoCleanerIssue(Booking $booking, Carbon $date): void
    {
        $dayName = $date->locale('nl')->dayName;

        // Check if identical issue already exists (deduplicate)
        $existingIssue = Issue::where('room_id', $booking->room_id)
            ->where('status', 'open')
            ->where('note', 'like', '%Boeking #'.$booking->id.'%')
            ->where('note', 'like', '%Geen schoonmakers beschikbaar%')
            ->first();

        if ($existingIssue) {
            return; // Don't create duplicate
        }

        Issue::create([
            'room_id' => $booking->room_id,
            'reported_by' => \App\Models\User::getSystemUserId(),
            'impact' => 'graag_snel',
            'note' => "URGENT: Geen schoonmakers beschikbaar op {$dayName} ({$date->format('d-m-Y')})\n\nBoeking #{$booking->id}\nKamer: {$booking->room->room_number}\nCheck-in: {$booking->check_in_datetime->format('d-m-Y H:i')}",
            'status' => 'open',
        ]);

        Log::warning("No cleaners available for booking {$booking->id} on {$date->toDateString()}");
    }

    /**
     * Create issue when task cannot be scheduled due to conflicts.
     */
    protected function createSchedulingConflictIssue(Booking $booking, Carbon $date, string $reason): void
    {
        // Check if identical issue already exists (deduplicate)
        $existingIssue = Issue::where('room_id', $booking->room_id)
            ->where('status', 'open')
            ->where('note', 'like', '%Boeking #'.$booking->id.'%')
            ->where('note', 'like', '%Kan schoonmaak niet inplannen%')
            ->first();

        if ($existingIssue) {
            return; // Don't create duplicate
        }

        Issue::create([
            'room_id' => $booking->room_id,
            'reported_by' => \App\Models\User::getSystemUserId(),
            'impact' => 'graag_snel',
            'note' => "URGENT: Kan schoonmaak niet inplannen - Alle schoonmakers zijn bezet\n\n"
                ."Boeking #{$booking->id}\n"
                ."Kamer: {$booking->room->room_number}\n"
                ."Check-in: {$booking->check_in_datetime->format('d-m-Y H:i')}\n\n"
                ."Conflicten:\n{$reason}\n\n"
                .'Oplossing: Voeg meer schoonmakers toe voor deze dag of verplaats boekingen.',
            'status' => 'open',
        ]);

        Log::warning("Could not schedule cleaning for booking {$booking->id} on {$date->toDateString()} - conflicts: {$reason}");
    }

    /**
     * Get day column name for database query.
     */
    protected function getDayColumn(int $dayOfWeek): string
    {
        return match ($dayOfWeek) {
            0 => 'works_sunday',
            1 => 'works_monday',
            2 => 'works_tuesday',
            3 => 'works_wednesday',
            4 => 'works_thursday',
            5 => 'works_friday',
            6 => 'works_saturday',
        };
    }
}
