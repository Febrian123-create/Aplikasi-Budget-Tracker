<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\BudgetService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BudgetController extends Controller
{
    public function __construct(private BudgetService $budgetService) {}

    public function index()
    {
        $userId = Auth::id();
        $budgets = $this->budgetService->getAll($userId);
        $categories = Category::all();
        $setting = $this->budgetService->getBudgetReminderSetting($userId);
        $isPremium = $this->isPremium();

        return view('budget.index', compact('budgets', 'categories', 'setting', 'isPremium'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => 'required|exists:category,category_id',
            'amount'      => 'required|numeric|min:1000',
            'period'      => 'required|in:bulanan,mingguan,tahunan',
            'start_date'  => 'required|date',
            'end_date'    => 'nullable|date|after:start_date',
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',
            'amount.required'      => 'Nominal budget wajib diisi.',
            'amount.min'           => 'Nominal budget minimal Rp 1.000.',
            'period.required'      => 'Periode wajib dipilih.',
            'start_date.required'  => 'Tanggal mulai wajib diisi.',
        ]);

        $validated['user_id'] = Auth::id();
        $this->budgetService->createOrUpdate(Auth::id(), $validated);

        return redirect()->route('budget.index')->with('success', 'Budget berhasil disimpan!');
    }

    public function destroy(int $id)
    {
        $deleted = $this->budgetService->delete($id, Auth::id());

        if (!$deleted) {
            return redirect()->route('budget.index')->with('error', 'Budget tidak ditemukan.');
        }

        return redirect()->route('budget.index')->with('success', 'Budget berhasil dihapus.');
    }

    public function settings()
    {
        $setting = $this->budgetService->getBudgetReminderSetting(Auth::id());
        $isPremium = $this->isPremium();

        return view('budget.settings', compact('setting', 'isPremium'));
    }

    public function saveSettings(Request $request)
    {
        $validated = $request->validate([
            'threshold'   => 'required|integer|min:50|max:100',
            'channels'    => 'nullable|array',
            'channels.*'  => 'string|in:email,popup,google_calendar',
        ], [
            'threshold.min' => 'Threshold minimal 50%.',
            'threshold.max' => 'Threshold maksimal 100%.',
        ]);

        $isPremium = $this->isPremium();
        if (!$isPremium) {
            $validated['channels'] = array_filter(
                $validated['channels'] ?? ['popup'],
                fn($c) => $c !== 'google_calendar'
            );
        }

        $this->budgetService->saveBudgetReminderSetting(Auth::id(), $validated);

        return redirect()->route('budget.settings')->with('success', 'Pengaturan alert budget berhasil disimpan!');
    }

    private function isPremium(): bool
    {
        $user = Auth::user();
        $membershipName = $user->membership ? $user->membership->membership_name : 'Free';
        return strtolower($membershipName) === 'premium';
    }
}
