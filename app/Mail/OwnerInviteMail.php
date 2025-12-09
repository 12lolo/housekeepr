<?php

namespace App\Mail;

use App\Models\User;
use App\Models\Hotel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OwnerInviteMail extends Mailable
{
    use Queueable, SerializesModels;

    public $owner;
    public $hotel;
    public $tempPassword;

    /**
     * Create a new message instance.
     */
    public function __construct(User $owner, ?Hotel $hotel, string $tempPassword)
    {
        $this->owner = $owner;
        $this->hotel = $hotel;
        $this->tempPassword = $tempPassword;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welkom bij HouseKeepr - Uw account is aangemaakt',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.owner-invite',
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
}
