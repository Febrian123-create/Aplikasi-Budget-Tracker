<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BudgetAlertMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public string $categoryName,
        public float $usagePercent,
        public float $spent,
        public float $limit
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "[BUNREK] Peringatan Budget: {$this->categoryName} sudah {$this->usagePercent}%",
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.budget-alert',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
