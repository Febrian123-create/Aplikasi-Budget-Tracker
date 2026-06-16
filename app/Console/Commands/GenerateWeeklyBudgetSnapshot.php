<?php

namespace App\Console\Commands;

use App\Services\BudgetReportService;
use Illuminate\Console\Command;

class GenerateWeeklyBudgetSnapshot extends Command
{
    protected $signature = 'budget-report:generate-weekly';
    protected $description = 'Buat snapshot status budget mingguan (overbudget/underbudget) untuk minggu Senin-Minggu yang baru selesai';

    public function __construct(private BudgetReportService $budgetReportService)
    {
        parent::__construct();
    }

    public function handle(): int
    {
        $this->info('Membuat snapshot budget mingguan...');
        $count = $this->budgetReportService->generateSnapshotsForCompletedWeek();
        $this->info("✓ {$count} snapshot berhasil dibuat/diperbarui.");
        return Command::SUCCESS;
    }
}
