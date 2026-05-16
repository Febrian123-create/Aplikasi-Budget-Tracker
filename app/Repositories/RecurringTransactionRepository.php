<?php

namespace App\Repositories;

use App\Models\RecurringTransaction;
use Illuminate\Support\Collection;
use Carbon\Carbon;

/**
 * RecurringTransactionRepository — Data Access Layer untuk Fitur 11.
 *
 * Single Responsibility: hanya interaksi database untuk
 * tabel recurring_transaction.
 */
class RecurringTransactionRepository
{
    /**
     * Ambil semua recurring milik user, urutkan next_run_date ASC.
     */
    public function findAll(int $userId): Collection
    {
        return RecurringTransaction::forUser($userId)
            ->with('category')
            ->orderBy('next_run_date', 'asc')
            ->get();
    }

    /**
     * Ambil recurring berdasarkan ID.
     */
    public function findById(int $recurringId): ?RecurringTransaction
    {
        return RecurringTransaction::with('category')->find($recurringId);
    }

    /**
     * Ambil semua recurring aktif yang jatuh tempo hari ini atau sebelumnya.
     * Digunakan oleh RecurringScheduler.
     */
    public function findDueToday(int $userId): Collection
    {
        return RecurringTransaction::forUser($userId)
            ->active()
            ->dueToday()
            ->with('category')
            ->get();
    }

    /**
     * Ambil semua recurring aktif yang jatuh tempo (untuk semua user).
     * Digunakan oleh scheduler cron job global.
     */
    public function findAllDueToday(): Collection
    {
        return RecurringTransaction::active()
            ->dueToday()
            ->with('category')
            ->get();
    }

    /**
     * Simpan recurring baru.
     */
    public function save(RecurringTransaction $recurring): RecurringTransaction
    {
        $recurring->save();
        return $recurring;
    }

    /**
     * Update data recurring.
     */
    public function update(RecurringTransaction $recurring, array $data): RecurringTransaction
    {
        $recurring->update($data);
        return $recurring->fresh('category');
    }

    /**
     * Hapus recurring.
     */
    public function delete(RecurringTransaction $recurring): bool
    {
        return $recurring->delete();
    }

    /**
     * Hitung jumlah recurring aktif milik user.
     * Digunakan untuk cek batas membership Free.
     */
    public function countActive(int $userId): int
    {
        return RecurringTransaction::forUser($userId)
            ->active()
            ->count();
    }

    /**
     * Ambil recurring berdasarkan ID dan user_id (keamanan).
     */
    public function findByIdAndUser(int $recurringId, int $userId): ?RecurringTransaction
    {
        return RecurringTransaction::forUser($userId)
            ->with('category')
            ->find($recurringId);
    }
}
