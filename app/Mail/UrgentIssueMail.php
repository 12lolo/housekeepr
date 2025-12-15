<?php

namespace App\Mail;

use App\Models\Issue;
use App\Models\Booking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class UrgentIssueMail extends Mailable
{
    use Queueable, SerializesModels;

    public $issue;
    public $booking;

    /**
     * Create a new message instance.
     */
    public function __construct(Issue $issue, Booking $booking = null)
    {
        $this->issue = $issue;
        $this->booking = $booking;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: '[URGENT] HouseKeepr - Probleem met kamer ' . $this->issue->room->room_number,
            replyTo: [
                config('mail.from.address', 'noreply@housekeepr.nl'),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.urgent-issue',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /**
     * Get the message headers.
     */
    public function headers(): \Illuminate\Mail\Mailables\Headers
    {
        return new \Illuminate\Mail\Mailables\Headers(
            text: [
                'X-Mailer' => 'HouseKeepr',
                'X-Priority' => '1',
                'Importance' => 'High',
                'X-MSMail-Priority' => 'High',
            ],
        );
    }
}
