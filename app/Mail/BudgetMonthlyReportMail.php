<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class BudgetMonthlyReportMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public User $user,
        public array $report,
        public string $pdfBinary,
        public string $pdfFilename
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: "[BUNREK] Laporan Budget Bulanan - {$this->report['monthLabel']}");
    }

    public function content(): Content
    {
        return new Content(view: 'emails.budget-monthly-report');
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdfBinary, $this->pdfFilename)->withMime('application/pdf'),
        ];
    }
}
