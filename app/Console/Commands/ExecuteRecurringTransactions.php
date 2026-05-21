<?php

namespace App\Console\Commands;

use App\Services\RecurringScheduler;
use Illuminate\Console\Command;


class ExecuteRecurringTransactions extends Command
{
    protected $signature = 'recurring:execute';

    protected $description = 'Eksekusi semua recurring transaction yang jatuh tempo hari ini';

    public function __construct(
        private RecurringScheduler $scheduler
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Mengecek recurring transaction yang jatuh tempo...');

        $executed = $this->scheduler->executeAllDue();

        if ($executed > 0) {
            $this->info("✓ {$executed} transaksi rutin berhasil dieksekusi.");
        } else {
            $this->info('Tidak ada recurring yang jatuh tempo hari ini.');
        }

        return Command::SUCCESS;
    }
}
