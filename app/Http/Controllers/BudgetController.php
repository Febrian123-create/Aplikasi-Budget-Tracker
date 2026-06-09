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
        $isWeekly = $request->input('period') === 'mingguan';

        $validated = $request->validate([
            'category_id' => 'required|exists:category,category_id',
            'amount'      => 'required|numeric|min:1000',
            'period'      => 'required|in:bulanan,mingguan,tahunan',
            'start_date'  => [$isWeekly ? 'required' : 'nullable', 'date'],
            'end_date'    => [$isWeekly ? 'required' : 'nullable', 'date', 'after:start_date'],
            'duration'    => ['nullable', 'integer', 'min:1', 'max:60'],
        ], [
            'category_id.required' => 'Kategori wajib dipilih.',
            'category_id.exists'   => 'Kategori tidak ditemukan.',
            'amount.required'      => 'Nominal budget wajib diisi.',
            'amount.min'           => 'Nominal budget minimal Rp 1.000.',
            'period.required'      => 'Periode wajib dipilih.',
            'start_date.required'  => 'Tanggal mulai wajib diisi untuk periode mingguan.',
            'end_date.required'    => 'Tanggal berakhir wajib diisi untuk periode mingguan.',
            'end_date.after'       => 'Tanggal berakhir harus setelah tanggal mulai.',
            'duration.min'         => 'Durasi minimal 1 periode.',
            'duration.max'         => 'Durasi maksimal 60 periode.',
        ]);

        $data = $this->normalizeBudgetPeriod($validated);
        $data['user_id'] = Auth::id();

        $this->budgetService->createOrUpdate(Auth::id(), $data);

        return redirect()->route('budget.index')->with('success', 'Budget berhasil disimpan!');
    }

    private function normalizeBudgetPeriod(array $validated): array
    {
        $period   = $validated['period'];
        $duration = $validated['duration'] ?? null;

        if ($period === 'mingguan') {
            return [
                'category_id' => $validated['category_id'],
                'amount'      => $validated['amount'],
                'period'      => $period,
                'duration'    => null,
                'start_date'  => $validated['start_date'],
                'end_date'    => $validated['end_date'],
            ];
        }

        $anchor = $period === 'tahunan'
            ? \Carbon\Carbon::now()->startOfYear()
            : \Carbon\Carbon::now()->startOfMonth();

        $endDate = null;
        if ($duration) {
            $endDate = ($period === 'tahunan'
                ? $anchor->copy()->addYears((int) $duration)
                : $anchor->copy()->addMonths((int) $duration)
            )->subDay()->toDateString();
        }

        return [
            'category_id' => $validated['category_id'],
            'amount'      => $validated['amount'],
            'period'      => $period,
            'duration'    => $duration ? (int) $duration : null,
            'start_date'  => $anchor->toDateString(),
            'end_date'    => $endDate,
        ];
    }

    public function update(Request $request, int $id)
    {
        $isWeekly = $request->input('period') === 'mingguan';

        $validated = $request->validate([
            'amount'     => 'required|numeric|min:1000',
            'period'     => 'required|in:bulanan,mingguan,tahunan',
            'start_date' => [$isWeekly ? 'required' : 'nullable', 'date'],
            'end_date'   => [$isWeekly ? 'required' : 'nullable', 'date', 'after:start_date'],
            'duration'   => ['nullable', 'integer', 'min:1', 'max:60'],
        ], [
            'amount.required'      => 'Nominal budget wajib diisi.',
            'amount.min'           => 'Nominal budget minimal Rp 1.000.',
            'period.required'      => 'Periode wajib dipilih.',
            'start_date.required'  => 'Tanggal mulai wajib diisi untuk periode mingguan.',
            'end_date.required'    => 'Tanggal berakhir wajib diisi untuk periode mingguan.',
            'end_date.after'       => 'Tanggal berakhir harus setelah tanggal mulai.',
        ]);

        // Preserve existing category_id; only amount/period/duration/dates are editable
        $existing = $this->budgetService->getAll(Auth::id())->firstWhere('budget_id', $id);
        if (!$existing) {
            return redirect()->route('budget.index')->with('error', 'Budget tidak ditemukan.');
        }

        $data = $this->normalizeBudgetPeriod(array_merge($validated, [
            'category_id' => $existing->category_id,
        ]));

        $updated = $this->budgetService->update($id, Auth::id(), $data);

        if (!$updated) {
            return redirect()->route('budget.index')->with('error', 'Budget tidak ditemukan.');
        }

        return redirect()->route('budget.index')->with('success', 'Budget berhasil diperbarui!');
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
