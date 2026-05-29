<?php

namespace App\Console\Commands;

use App\Services\ReminderService;
use Illuminate\Console\Command;

class SendRecurringReminders extends Command
{
    protected $signature = 'reminders:send';

    protected $description = 'Kirim reminder untuk recurring transaction yang mendekati tanggal eksekusi';

    public function __construct(private ReminderService $reminderService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Mengecek reminder yang perlu dikirim hari ini...');

        $sent = $this->reminderService->processDueReminders();

        if ($sent > 0) {
            $this->info("✓ {$sent} reminder berhasil dikirim.");
        } else {
            $this->info('Tidak ada reminder yang perlu dikirim hari ini.');
        }

        return Command::SUCCESS;
    }
}
