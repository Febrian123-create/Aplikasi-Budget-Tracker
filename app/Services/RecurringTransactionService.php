<?php

namespace App\Services;

use App\Factories\TransactionFactory;
use App\Helpers\RecurringHelper;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Observers\TransactionSubject;
use App\Repositories\RecurringTransactionRepository;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

/**
 * RecurringTransactionService — Business Logic Layer untuk Fitur 11.
 *
 * Single Responsibility: hanya CRUD dan eksekusi recurring.
 * Menerima repository via Dependency Injection (constructor).
 */
class RecurringTransactionService
{
    public function __construct(
        private RecurringTransactionRepository $recurringRepo
    ) {}

    /**
     * Buat recurring baru via TransactionFactory.
     *
     * @throws \Exception jika validasi gagal
     */
    public function create(int $userId, array $data): RecurringTransaction
    {
        $data['user_id'] = $userId;

        return TransactionFactory::createRecurringTransaction($data);
    }

    /**
     * Ambil semua recurring milik user.
     */
    public function getAll(int $userId): Collection
    {
        return $this->recurringRepo->findAll($userId);
    }

    /**
     * Ambil satu recurring berdasarkan ID dan user.
     */
    public function getById(int $recurringId, int $userId): ?RecurringTransaction
    {
        return $this->recurringRepo->findByIdAndUser($recurringId, $userId);
    }

    /**
     * Update data recurring.
     * Jika frequency berubah, hitung ulang next_run_date.
     */
    public function update(int $recurringId, int $userId, array $data): ?RecurringTransaction
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring) {
            return null;
        }

        // Cek apakah frequency berubah → hitung ulang next_run_date
        $frequencyChanged = isset($data['frequency']) && $data['frequency'] !== $recurring->frequency;

        $updateData = [];

        if (isset($data['amount'])) {
            $updateData['amount'] = $data['amount'];
        }
        if (isset($data['description'])) {
            $updateData['description'] = $data['description'];
        }
        if (isset($data['frequency'])) {
            $updateData['frequency'] = $data['frequency'];
        }
        if (isset($data['category_id'])) {
            $updateData['category_id'] = $data['category_id'];
        }
        if (isset($data['amount_type'])) {
            $updateData['amount_type'] = $data['amount_type'];
        }
        if (array_key_exists('end_date', $data)) {
            $updateData['end_date'] = $data['end_date'];
        }

        // Hitung ulang next_run_date jika frequency berubah
        if ($frequencyChanged) {
            $baseDate = Carbon::today();
            $newFrequency = $data['frequency'];
            $updateData['next_run_date'] = $baseDate->toDateString();
        }

        return $this->recurringRepo->update($recurring, $updateData);
    }

    /**
     * Hapus recurring. Transaksi yang sudah tercatat tetap ada.
     */
    public function delete(int $recurringId, int $userId): bool
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring) {
            return false;
        }

        return $this->recurringRepo->delete($recurring);
    }

    /**
     * Toggle status antara 'aktif' dan 'dijeda'.
     * Saat diaktifkan kembali: next_run_date dihitung ulang dari hari ini.
     */
    public function toggleStatus(int $recurringId, int $userId): ?RecurringTransaction
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring) {
            return null;
        }

        if ($recurring->status === 'aktif') {
            // Jeda recurring
            return $this->recurringRepo->update($recurring, ['status' => 'dijeda']);
        }

        if ($recurring->status === 'dijeda') {
            // Aktifkan kembali — next_run_date dihitung dari hari ini
            $nextRun = Carbon::today()->toDateString();

            return $this->recurringRepo->update($recurring, [
                'status'        => 'aktif',
                'next_run_date' => $nextRun,
            ]);
        }

        return $recurring;
    }

    /**
     * Eksekusi satu recurring: buat Transaction baru & update next_run_date.
     *
     * Transaksi dibuat via TransactionFactory, lalu
     * TransactionSubject.notifyObservers() dipanggil agar
     * saldo dan grafik (Fitur 8) otomatis terupdate.
     */
    public function executeRecurring(int $recurringId): ?Transaction
    {
        $recurring = $this->recurringRepo->findById($recurringId);

        if (!$recurring || $recurring->status !== 'aktif') {
            return null;
        }

        try {
            // 1. Buat Transaction baru dari data recurring
            $transaction = TransactionFactory::createTransactionFromRecurring($recurring);

            // 2. Notify observers (Observer Pattern — saldo & chart refresh)
            TransactionSubject::getInstance()->notifyObservers('created', $transaction);

            // 3. Hitung next_run_date berikutnya
            $newNextRun = RecurringHelper::calculateNextRunDate(
                $recurring->next_run_date,
                $recurring->frequency
            );

            // 4. Cek apakah sudah expired
            if (RecurringHelper::isExpired($newNextRun, $recurring->end_date)) {
                $this->recurringRepo->update($recurring, [
                    'status'        => 'selesai',
                    'next_run_date' => $newNextRun->toDateString(),
                ]);
            } else {
                $this->recurringRepo->update($recurring, [
                    'next_run_date' => $newNextRun->toDateString(),
                ]);
            }

            return $transaction;
        } catch (\Exception $e) {
            // Log error, jangan crash app
            Log::error('Gagal eksekusi recurring #' . $recurringId . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Hitung jumlah recurring aktif milik user.
     */
    public function countActive(int $userId): int
    {
        return $this->recurringRepo->countActive($userId);
    }

    /**
     * Ambil recurring yang jatuh tempo untuk user tertentu.
     */
    public function getDueToday(int $userId): Collection
    {
        return $this->recurringRepo->findDueToday($userId);
    }
}
