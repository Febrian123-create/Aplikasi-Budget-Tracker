<?php

namespace App\Http\Controllers;

use App\Helpers\RecurringHelper;
use App\Http\Requests\RecurringTransactionRequest;
use App\Models\Category;
use App\Services\RecurringScheduler;
use App\Services\RecurringTransactionService;
use App\Services\ReminderService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class RecurringTransactionController extends Controller
{
    public function __construct(
        private RecurringTransactionService $recurringService,
        private RecurringScheduler $scheduler,
        private ReminderService $reminderService
    ) {}

    public function index()
    {
        $userId = Auth::id();

        // Hanya hitung jumlah pending (tidak auto-execute)
        $pendingCount = $this->scheduler->executeDueForUser($userId);

        $recurrings = $this->recurringService->getAll($userId);
        $pendingConfirmations = $this->recurringService->getPendingConfirmations($userId);

        $categories = Category::all();
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Premium';
        $frequencies = RecurringHelper::getAvailableFrequencies($membershipName);
        $activeCount = $this->recurringService->countActive($userId);
        $isPremium = true;
        $canCreate = true;

        return view('recurring.index', compact(
            'recurrings',
            'pendingConfirmations',
            'categories',
            'frequencies',
            'pendingCount',
            'activeCount',
            'isPremium',
            'canCreate',
            'membershipName'
        ));
    }

    public function store(RecurringTransactionRequest $request)
    {
        $userId = Auth::id();

        $data = $request->validated();
        $data['reminder_enabled'] = $request->boolean('reminder_enabled');
        if (($data['amount_type'] ?? null) === 'pemasukan') {
            $data['category_id'] = 10;
        }

        $startDate = Carbon::parse($data['start_date']);
        $warningMessage = null;
        if ($startDate->isBefore(Carbon::today())) {
            $warningMessage = 'Tanggal mulai sudah lewat. Transaksi pertama akan langsung dicatat hari ini.';
        }

        $this->recurringService->create($userId, $data);

        $successMessage = 'Transaksi rutin berhasil ditambahkan!';
        if ($warningMessage) {
            $successMessage .= ' ' . $warningMessage;
        }

        return redirect()->route('recurring.index')->with('success', $successMessage);
    }

    public function edit(int $id)
    {
        $userId = Auth::id();
        $recurring = $this->recurringService->getById($id, $userId);

        if (!$recurring) {
            return redirect()->route('recurring.index')->with('error', 'Transaksi rutin tidak ditemukan.');
        }

        $categories = Category::all();
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Premium';
        $frequencies = RecurringHelper::getAvailableFrequencies($membershipName);
        $isPremium = true;

        // Load reminder config jika ada
        $reminder = $recurring->reminder;

        return view('recurring.edit', compact('recurring', 'categories', 'frequencies', 'isPremium', 'reminder'));
    }

    public function update(RecurringTransactionRequest $request, int $id)
    {
        $userId = Auth::id();

        $data = $request->validated();
        $data['reminder_enabled'] = $request->boolean('reminder_enabled');
        if (($data['amount_type'] ?? null) === 'pemasukan') {
            $data['category_id'] = 10;
        }

        $recurring = $this->recurringService->update($id, $userId, $data);

        if (!$recurring) {
            return redirect()->route('recurring.index')->with('error', 'Transaksi rutin tidak ditemukan.');
        }

        return redirect()->route('recurring.index')->with('success', 'Transaksi rutin berhasil diperbarui!');
    }

    public function destroy(int $id)
    {
        $userId = Auth::id();
        $deleted = $this->recurringService->delete($id, $userId);

        if (!$deleted) {
            return redirect()->route('recurring.index')->with('error', 'Transaksi rutin tidak ditemukan.');
        }

        return redirect()->route('recurring.index')->with('success', 'Transaksi rutin berhasil dihapus!');
    }

    public function toggleStatus(int $id)
    {
        $userId = Auth::id();
        $recurring = $this->recurringService->toggleStatus($id, $userId);

        if (!$recurring) {
            return redirect()->route('recurring.index')->with('error', 'Transaksi rutin tidak ditemukan.');
        }

        $statusLabel = $recurring->status === 'aktif' ? 'diaktifkan' : 'dijeda';
        return redirect()->route('recurring.index')->with('success', "Transaksi rutin berhasil {$statusLabel}!");
    }

    /**
     * Konfirmasi pembayaran recurring (user menyatakan sudah dibayar).
     * Membuat 1 transaksi dan advance ke periode berikutnya.
     */
    public function confirmPayment(int $id)
    {
        $userId = Auth::id();
        $transaction = $this->recurringService->confirmPayment($id, $userId);

        if (!$transaction) {
            return redirect()->route('recurring.index')
                ->with('error', 'Gagal mengkonfirmasi pembayaran. Pastikan transaksi rutin masih aktif dan menunggu konfirmasi.');
        }

        return redirect()->route('recurring.index')
            ->with('success', '✅ Pembayaran dikonfirmasi! Transaksi telah dicatat ke riwayat.');
    }

    /**
     * Lewati (skip) periode ini tanpa membuat transaksi.
     */
    public function skipPayment(int $id)
    {
        $userId = Auth::id();
        $recurring = $this->recurringService->skipPayment($id, $userId);

        if (!$recurring) {
            return redirect()->route('recurring.index')
                ->with('error', 'Gagal melewati periode ini. Pastikan transaksi rutin masih aktif.');
        }

        return redirect()->route('recurring.index')
            ->with('success', '⏭ Periode ini dilewati. Pengingat berikutnya akan dikirim pada periode selanjutnya.');
    }

    public function unreadPopups()
    {
        $userId = Auth::id();
        $logs = $this->reminderService->getUnreadPopups($userId);

        $data = $logs->map(function ($log) {
            $title = $log->type === 'budget'
                ? 'Peringatan Budget'
                : 'Pengingat Transaksi Rutin';

            $body = '';
            if ($log->type === 'recurring' && $log->recurringTransaction) {
                $rec = $log->recurringTransaction;
                $label = $log->days_before === 0 ? 'hari ini' : "H-{$log->days_before}";
                $body = "Transaksi '{$rec->description}' (Rp " . number_format($rec->amount, 0, ',', '.') . ") jatuh {$label}.";
            } elseif ($log->type === 'budget') {
                $body = 'Pengeluaran kamu mendekati atau melebihi batas budget.';
            }

            return [
                'id'    => $log->id,
                'type'  => $log->type,
                'title' => $title,
                'body'  => $body,
            ];
        });

        return response()->json($data);
    }

    public function markPopupRead(int $logId)
    {
        $this->reminderService->markPopupAsRead($logId);
        return response()->json(['ok' => true]);
    }
}
