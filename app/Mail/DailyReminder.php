<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\URL;

class DailyReminder extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(protected User $user)
    {
        //
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'How was your day?',
        );
    }

    public function headers(): Headers
    {
        // Lets mail clients offer a one-click unsubscribe (RFC 8058).
        return new Headers(text: [
            'List-Unsubscribe' => '<' . $this->unsubscribeUrl() . '>',
            'List-Unsubscribe-Post' => 'List-Unsubscribe=One-Click',
        ]);
    }

    public function content(): Content
    {
        return new Content(
            markdown: 'mail.daily-reminder',
            with: [
                'unsubscribeUrl' => $this->unsubscribeUrl(),
            ]
        );
    }

    public function attachments(): array
    {
        return [];
    }

    private function unsubscribeUrl(): string
    {
        // Signed over the path only, so it survives http/https or host differences behind the proxy.
        return url(URL::signedRoute('reminders.unsubscribe', ['user' => $this->user->id], absolute: false));
    }
}
