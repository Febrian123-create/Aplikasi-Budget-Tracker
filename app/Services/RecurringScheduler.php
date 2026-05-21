<?php

namespace App\Services;

use App\Repositories\RecurringTransactionRepository;
use Illuminate\Support\Facades\Log;


class RecurringScheduler
{
    public function __construct(
        private RecurringTransactionService $recurringService,
        private RecurringTransactionRepository $recurringRepo
    ) {}

    
    public function executeDueForUser(int $userId): int
    {
        $dueRecurrings = $this->recurringRepo->findDueToday($userId);
        $executed = 0;

        foreach ($dueRecurrings as $recurring) {
            try {
                $transaction = $this->recurringService->executeRecurring($recurring->recurring_id);

                if ($transaction) {
                    $executed++;
                    Log::info("Recurring #{$recurring->recurring_id} berhasil dieksekusi untuk user #{$userId}");
                }
            } catch (\Exception $e) {
                
                Log::error("Scheduler gagal eksekusi recurring #{$recurring->recurring_id}: " . $e->getMessage());
            }
        }

        return $executed;
    }

    
    public function executeAllDue(): int
    {
        $dueRecurrings = $this->recurringRepo->findAllDueToday();
        $executed = 0;

        foreach ($dueRecurrings as $recurring) {
            try {
                $transaction = $this->recurringService->executeRecurring($recurring->recurring_id);

                if ($transaction) {
                    $executed++;
                    Log::info("Cron: Recurring #{$recurring->recurring_id} berhasil dieksekusi");
                }
            } catch (\Exception $e) {
                Log::error("Cron: Gagal eksekusi recurring #{$recurring->recurring_id}: " . $e->getMessage());
            }
        }

        if ($executed > 0) {
            Log::info("Cron: Total {$executed} recurring berhasil dieksekusi");
        }

        return $executed;
    }
}
