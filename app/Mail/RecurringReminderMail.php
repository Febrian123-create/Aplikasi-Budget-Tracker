<?php

namespace App\Mail;

use App\Models\RecurringTransaction;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecurringReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public RecurringTransaction $recurring,
        public int $daysBefore,
        public ?string $customMessage = null
    ) {}

    public function envelope(): Envelope
    {
        $label = $this->daysBefore === 0 ? 'Hari Ini' : "H-{$this->daysBefore}";
        return new Envelope(
            subject: "[BUNREK] Pengingat Transaksi Rutin - {$label}: {$this->recurring->description}",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.recurring-reminder',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
