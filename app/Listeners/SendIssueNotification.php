<?php

namespace App\Listeners;

use App\Events\BlockingIssueCreated;
use App\Mail\UrgentIssueMail;
use Illuminate\Support\Facades\Mail;

class SendIssueNotification
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(BlockingIssueCreated $event): void
    {
        $issue = $event->issue;
        $room = $issue->room;

        // Send email to hotel owner if notifications are enabled
        if ($room && $room->hotel && $room->hotel->owner && $room->hotel->owner->notifications_enabled) {
            try {
                Mail::to($room->hotel->owner->email)->send(new UrgentIssueMail($issue));
            } catch (\Exception $e) {
                \Log::error("Failed to send issue notification email: {$e->getMessage()}");
            }
        }
    }
}
