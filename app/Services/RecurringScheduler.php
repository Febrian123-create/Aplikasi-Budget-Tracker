<?php

namespace App\Services;

use App\Repositories\RecurringTransactionRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * RecurringScheduler — Hanya mendeteksi recurring yang perlu konfirmasi.
 *
 * CATATAN PENTING:
 * Sistem ini TIDAK lagi mengeksekusi (membuat transaksi) secara otomatis.
 * Recurring hanya berfungsi sebagai PENGINGAT (reminder).
 * Transaksi dibuat HANYA setelah user mengkonfirmasi pembayaran
 * melalui RecurringTransactionController::confirmPayment().
 */
class RecurringScheduler
{
    public function __construct(
        private RecurringTransactionService $recurringService,
        private RecurringTransactionRepository $recurringRepo
    ) {}

    /**
     * Hitung jumlah recurring milik user yang menunggu konfirmasi hari ini.
     * TIDAK membuat transaksi otomatis.
     *
     * @return int Jumlah recurring yang perlu dikonfirmasi
     */
    public function executeDueForUser(int $userId): int
    {
        // Kembalikan jumlah pending confirmations (bukan jumlah transaksi yang dibuat)
        $pending = $this->recurringService->getPendingConfirmations($userId);
        return $pending->count();
    }

    /**
     * Cron command: hanya log recurring yang perlu dikonfirmasi hari ini.
     * TIDAK membuat transaksi otomatis.
     */
    public function executeAllDue(): int
    {
        $dueRecurrings = $this->recurringRepo->findAllDueToday();
        $count = $dueRecurrings->count();

        if ($count > 0) {
            Log::info("Cron: {$count} recurring menunggu konfirmasi dari user.");
        }

        return $count;
    }
}
