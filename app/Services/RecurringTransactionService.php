<?php

namespace App\Services;

use App\Factories\TransactionFactory;
use App\Helpers\RecurringHelper;
use App\Models\RecurringTransaction;
use App\Models\Transaction;
use App\Observers\TransactionSubject;
use App\Repositories\RecurringTransactionRepository;
use App\Services\ReminderService;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;


class RecurringTransactionService
{
    public function __construct(
        private RecurringTransactionRepository $recurringRepo,
        private ReminderService $reminderService
    ) {}

    
    public function create(int $userId, array $data): RecurringTransaction
    {
        $data['user_id'] = $userId;
        // Pastikan confirmation_status dimulai sebagai pending
        $data['confirmation_status'] = 'pending';

        $recurring = TransactionFactory::createRecurringTransaction($data);

        if (!empty($data['reminder_enabled'])) {
            $this->reminderService->saveReminderConfig($recurring, $data);
        }

        return $recurring;
    }

    
    public function getAll(int $userId): Collection
    {
        return $this->recurringRepo->findAll($userId);
    }

    
    public function getById(int $recurringId, int $userId): ?RecurringTransaction
    {
        return $this->recurringRepo->findByIdAndUser($recurringId, $userId);
    }

    
    public function update(int $recurringId, int $userId, array $data): ?RecurringTransaction
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring) {
            return null;
        }

        
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

        
        if ($frequencyChanged) {
            $baseDate = Carbon::today();
            $newFrequency = $data['frequency'];
            $updateData['next_run_date'] = $baseDate->toDateString();
        }

        $updated = $this->recurringRepo->update($recurring, $updateData);

        // Selalu sync reminder config (simpan ulang jika enabled, hapus jika disabled)
        if (!empty($data['reminder_enabled'])) {
            $this->reminderService->saveReminderConfig($updated, $data);
        } else {
            $this->reminderService->deleteReminderConfig($updated->recurring_id);
        }

        return $updated;
    }

    
    public function delete(int $recurringId, int $userId): bool
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring) {
            return false;
        }

        $this->reminderService->deleteReminderConfig($recurringId);

        return $this->recurringRepo->delete($recurring);
    }

    
    public function toggleStatus(int $recurringId, int $userId): ?RecurringTransaction
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring) {
            return null;
        }

        if ($recurring->status === 'aktif') {
            
            return $this->recurringRepo->update($recurring, ['status' => 'dijeda']);
        }

        if ($recurring->status === 'dijeda') {
            
            $nextRun = Carbon::today()->toDateString();

            return $this->recurringRepo->update($recurring, [
                'status'        => 'aktif',
                'next_run_date' => $nextRun,
            ]);
        }

        return $recurring;
    }

    /**
     * Konfirmasi pembayaran oleh user.
     * Membuat 1 transaksi dengan tanggal = last_due_date (tanggal jatuh tempo asli),
     * lalu advance next_run_date ke periode berikutnya.
     */
    public function confirmPayment(int $recurringId, int $userId): ?Transaction
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring || $recurring->status !== 'aktif') {
            return null;
        }

        if ($recurring->confirmation_status !== 'pending') {
            return null;
        }

        try {
            // Tanggal transaksi = tanggal jatuh tempo asli (last_due_date atau next_run_date)
            $dueDate = $recurring->last_due_date ?? $recurring->next_run_date;

            // Buat transaksi
            $transaction = TransactionFactory::createTransactionFromRecurring($recurring, $dueDate);

            // Notifikasi observer (untuk budget alert dll)
            TransactionSubject::getInstance()->notifyObservers('created', $transaction);

            // Advance next_run_date ke periode berikutnya
            $newNextRun = RecurringHelper::calculateNextRunDate(
                $recurring->next_run_date,
                $recurring->frequency
            );

            $updateData = [
                'confirmation_status' => 'pending',
                'confirmed_at'        => now(),
                'last_due_date'       => $newNextRun->toDateString(),
                'next_run_date'       => $newNextRun->toDateString(),
            ];

            // Cek apakah periode berikutnya sudah expired
            if (RecurringHelper::isExpired($newNextRun, $recurring->end_date)) {
                $updateData['status'] = 'selesai';
            }

            $this->recurringRepo->update($recurring, $updateData);

            return $transaction;
        } catch (\Exception $e) {
            Log::error('Gagal konfirmasi recurring #' . $recurringId . ': ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Lewati (skip) periode ini tanpa membuat transaksi.
     * Advance next_run_date ke periode berikutnya.
     */
    public function skipPayment(int $recurringId, int $userId): ?RecurringTransaction
    {
        $recurring = $this->recurringRepo->findByIdAndUser($recurringId, $userId);

        if (!$recurring || $recurring->status !== 'aktif') {
            return null;
        }

        if ($recurring->confirmation_status !== 'pending') {
            return null;
        }

        // Advance next_run_date ke periode berikutnya
        $newNextRun = RecurringHelper::calculateNextRunDate(
            $recurring->next_run_date,
            $recurring->frequency
        );

        $updateData = [
            'confirmation_status' => 'pending',
            'last_due_date'       => $newNextRun->toDateString(),
            'next_run_date'       => $newNextRun->toDateString(),
        ];

        if (RecurringHelper::isExpired($newNextRun, $recurring->end_date)) {
            $updateData['status'] = 'selesai';
        }

        return $this->recurringRepo->update($recurring, $updateData);
    }

    /**
     * Ambil semua recurring milik user yang menunggu konfirmasi (jatuh tempo dan belum dikonfirmasi).
     */
    public function getPendingConfirmations(int $userId): Collection
    {
        return RecurringTransaction::forUser($userId)
            ->pendingConfirmation()
            ->with('category')
            ->get();
    }

    
    public function countActive(int $userId): int
    {
        return $this->recurringRepo->countActive($userId);
    }

    
    public function getDueToday(int $userId): Collection
    {
        return $this->recurringRepo->findDueToday($userId);
    }

    /**
     * Internal: eksekusi langsung recurring (dipakai oleh confirmPayment).
     * Tidak lagi dipanggil secara otomatis oleh scheduler.
     *
     * @deprecated Gunakan confirmPayment() untuk konfirmasi manual oleh user.
     */
    public function executeRecurring(int $recurringId): ?Transaction
    {
        $recurring = $this->recurringRepo->findById($recurringId);

        if (!$recurring || $recurring->status !== 'aktif') {
            return null;
        }

        try {
            
            $transaction = TransactionFactory::createTransactionFromRecurring($recurring);

            
            TransactionSubject::getInstance()->notifyObservers('created', $transaction);

            
            $newNextRun = RecurringHelper::calculateNextRunDate(
                $recurring->next_run_date,
                $recurring->frequency
            );

            
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
            
            Log::error('Gagal eksekusi recurring #' . $recurringId . ': ' . $e->getMessage());
            return null;
        }
    }
}
