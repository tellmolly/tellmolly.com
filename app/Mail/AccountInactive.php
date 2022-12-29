<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AccountInactive extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected User $user, protected int $daysInactive, protected int $daysRemaining)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Account Inactive',
        );
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.account-inactive',
            with: [
                'inactive' => $this->daysInactive,
                'remaining' => $this->daysRemaining
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
