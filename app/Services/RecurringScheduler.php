<?php

namespace App\Services;

use App\Repositories\RecurringTransactionRepository;
use Illuminate\Support\Facades\Log;

/**
 * RecurringScheduler — Scheduler untuk Fitur 11.
 *
 * Single Responsibility: hanya cek & eksekusi jadwal.
 * Berjalan setiap hari (cron job) atau saat app diakses
 * untuk mengecek recurring mana yang jatuh tempo.
 */
class RecurringScheduler
{
    public function __construct(
        private RecurringTransactionService $recurringService,
        private RecurringTransactionRepository $recurringRepo
    ) {}

    /**
     * Eksekusi semua recurring yang jatuh tempo untuk user tertentu.
     *
     * Dipanggil saat user mengakses halaman recurring
     * (fallback jika cron job tidak berjalan).
     *
     * @return int Jumlah transaksi yang berhasil dieksekusi
     */
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
                // Log error, lanjut ke recurring berikutnya
                Log::error("Scheduler gagal eksekusi recurring #{$recurring->recurring_id}: " . $e->getMessage());
            }
        }

        return $executed;
    }

    /**
     * Eksekusi semua recurring yang jatuh tempo (global — untuk cron job).
     *
     * Dipanggil oleh artisan command / cron job harian.
     *
     * @return int Jumlah transaksi yang berhasil dieksekusi
     */
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
