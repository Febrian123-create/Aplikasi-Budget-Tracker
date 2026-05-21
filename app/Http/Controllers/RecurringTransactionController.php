<?php

namespace App\Http\Controllers;

use App\Helpers\RecurringHelper;
use App\Http\Requests\RecurringTransactionRequest;
use App\Models\Category;
use App\Services\RecurringScheduler;
use App\Services\RecurringTransactionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;


class RecurringTransactionController extends Controller
{
    public function __construct(
        private RecurringTransactionService $recurringService,
        private RecurringScheduler $scheduler
    ) {}

    
    public function index()
    {
        $userId = Auth::id();

        
        $executedCount = $this->scheduler->executeDueForUser($userId);

        
        $recurrings = $this->recurringService->getAll($userId);

        
        $categories = Category::all();
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Free';
        $frequencies = RecurringHelper::getAvailableFrequencies($membershipName);
        $activeCount = $this->recurringService->countActive($userId);
        $isPremium = strtolower($membershipName) === 'premium';
        $maxFreeRecurring = 3;
        $canCreate = $isPremium || $activeCount < $maxFreeRecurring;

        return view('recurring.index', compact(
            'recurrings',
            'categories',
            'frequencies',
            'executedCount',
            'activeCount',
            'isPremium',
            'maxFreeRecurring',
            'canCreate',
            'membershipName'
        ));
    }

    
    public function store(RecurringTransactionRequest $request)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Free';
        $isPremium = strtolower($membershipName) === 'premium';

        
        if (!$isPremium) {
            $activeCount = $this->recurringService->countActive($userId);
            if ($activeCount >= 3) {
                return redirect()->back()
                    ->with('error', 'Kamu sudah mencapai batas 3 recurring. Upgrade ke Premium untuk menambah lebih banyak.')
                    ->withInput();
            }

            
            $allowedFrequencies = ['bulanan', 'tahunan'];
            if (!in_array($request->frequency, $allowedFrequencies)) {
                return redirect()->back()
                    ->with('error', 'User Free hanya bisa menggunakan frekuensi Bulanan dan Tahunan. Upgrade ke Premium untuk semua frekuensi.')
                    ->withInput();
            }
        }

        $data = $request->validated();

        
        $startDate = Carbon::parse($data['start_date']);
        $warningMessage = null;
        if ($startDate->isBefore(Carbon::today())) {
            $warningMessage = 'Tanggal mulai sudah lewat. Transaksi pertama akan langsung dicatat hari ini.';
        }

        $recurring = $this->recurringService->create($userId, $data);

        $successMessage = 'Transaksi rutin berhasil ditambahkan!';
        if ($warningMessage) {
            $successMessage .= ' ' . $warningMessage;
        }

        return redirect()->route('recurring.index')->with('success', $successMessage);
    }

    
    public function update(RecurringTransactionRequest $request, int $id)
    {
        $userId = Auth::id();
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Free';
        $isPremium = strtolower($membershipName) === 'premium';

        
        if (!$isPremium) {
            $allowedFrequencies = ['bulanan', 'tahunan'];
            if (!in_array($request->frequency, $allowedFrequencies)) {
                return redirect()->back()
                    ->with('error', 'User Free hanya bisa menggunakan frekuensi Bulanan dan Tahunan.')
                    ->withInput();
            }
        }

        $data = $request->validated();
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

        return redirect()->route('recurring.index')->with('success', 'Transaksi rutin berhasil dihapus! Transaksi yang sudah tercatat tidak terhapus.');
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

    
    public function edit(int $id)
    {
        $userId = Auth::id();
        $recurring = $this->recurringService->getById($id, $userId);

        if (!$recurring) {
            return redirect()->route('recurring.index')->with('error', 'Transaksi rutin tidak ditemukan.');
        }

        $categories = Category::all();
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Free';
        $frequencies = RecurringHelper::getAvailableFrequencies($membershipName);

        return view('recurring.edit', compact('recurring', 'categories', 'frequencies'));
    }
}
