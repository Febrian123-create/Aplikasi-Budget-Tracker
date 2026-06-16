<?php

namespace App\Services;

use App\Helpers\ChartHelper;
use App\Models\Budget;
use App\Models\Transaction;
use App\Models\TransactionType;
use App\Repositories\BudgetReportRepository;
use Carbon\Carbon;

class BudgetReportService
{
    private ?int $expenseTypeId = null;

    public function __construct(private BudgetReportRepository $reportRepo) {}

    public function getMonthlyReport(int $userId, int $bulan, int $tahun): array
    {
        $monthStart = Carbon::create($tahun, $bulan, 1)->startOfMonth();
        $monthEnd   = $monthStart->copy()->endOfMonth();
        $today      = Carbon::today();
        $budgets    = $this->reportRepo->findActiveMonthlyBudgetsByUser($userId);
        $weeks      = $this->enumerateWeeksForMonth($monthStart, $monthEnd);

        $categories = [];
        foreach ($budgets as $budget) {
            $weekRows   = [];
            $totalSpent = 0.0;

            foreach ($weeks as [$weekStart, $weekEnd]) {
                if ($weekStart->gt($today)) continue;

                $isLive   = $today->between($weekStart, $weekEnd);
                $snapshot = $isLive ? null : $this->reportRepo->findSnapshot($userId, $budget->category_id, $weekStart->toDateString());

                if ($snapshot) {
                    $allocated = (float) $snapshot->allocated_amount;
                    $spent     = (float) $snapshot->spent_amount;
                    $status    = $snapshot->status;
                } else {
                    $c         = $this->computeWeekSnapshot($budget, $weekStart, $weekEnd, $monthStart, $monthEnd);
                    $allocated = $c['allocated_amount'];
                    $spent     = $c['spent_amount'];
                    $status    = $c['status'];
                }

                $totalSpent += $spent;
                $weekPct     = $allocated > 0 ? min(100, round(($spent / $allocated) * 100)) : ($spent > 0 ? 100 : 0);

                $weekRows[] = [
                    'week_start'          => $weekStart->copy(),
                    'week_end'            => $weekEnd->copy(),
                    'label'               => $weekStart->format('d M') . ' - ' . $weekEnd->format('d M Y'),
                    'allocated'           => $allocated,
                    'spent'               => $spent,
                    'allocated_formatted' => ChartHelper::formatRupiah($allocated),
                    'spent_formatted'     => ChartHelper::formatRupiah($spent),
                    'week_pct'            => $weekPct,
                    'status'              => $status,
                    'isOverBudget'        => $status === 'overbudget',
                    'isLive'              => $isLive,
                ];
            }

            $monthlyAmount = (float) $budget->amount;
            $monthlyStatus = $totalSpent > $monthlyAmount ? 'overbudget' : 'underbudget';
            $monthlyPct    = $monthlyAmount > 0 ? round(($totalSpent / $monthlyAmount) * 100, 1) : 0;

            $categories[] = [
                'budget_id'                => $budget->budget_id,
                'category_id'              => $budget->category_id,
                'category_name'            => $budget->category->category_name ?? "Kategori #{$budget->category_id}",
                'monthly_amount'           => $monthlyAmount,
                'monthly_amount_formatted' => ChartHelper::formatRupiah($monthlyAmount),
                'monthly_spent'            => $totalSpent,
                'monthly_spent_formatted'  => ChartHelper::formatRupiah($totalSpent),
                'monthly_status'           => $monthlyStatus,
                'isOverBudget'             => $monthlyStatus === 'overbudget',
                'monthly_pct'              => $monthlyPct,
                'monthly_pct_bar'          => min(100, $monthlyPct),
                'weeks'                    => $weekRows,
            ];
        }

        return [
            'monthLabel'  => ChartHelper::formatBulanLengkap($bulan) . ' ' . $tahun,
            'bulan'       => $bulan,
            'tahun'       => $tahun,
            'categories'  => $categories,
            'isEmpty'     => empty($categories),
            'generatedAt' => Carbon::now()->format('d-m-Y H:i:s'),
        ];
    }

    public function computeWeekSnapshot(Budget $budget, Carbon $weekStart, Carbon $weekEnd, Carbon $monthStart, Carbon $monthEnd): array
    {
        $daysInMonth = $monthStart->daysInMonth;
        $overlapDays = $this->overlapDays($weekStart, $weekEnd, $monthStart, $monthEnd);
        $allocated   = $daysInMonth > 0 ? ((float) $budget->amount) * ($overlapDays / $daysInMonth) : 0.0;

        $today        = Carbon::today();
        $effectiveEnd = $weekEnd->gt($today) ? $today : $weekEnd;
        $spent        = $this->sumExpenses($budget->user_id, $budget->category_id, $weekStart, $effectiveEnd);

        return [
            'allocated_amount' => round($allocated, 2),
            'spent_amount'     => round($spent, 2),
            'status'           => $spent > $allocated ? 'overbudget' : 'underbudget',
        ];
    }

    public function generateSnapshotsForCompletedWeek(?Carbon $referenceDate = null): int
    {
        $reference  = ($referenceDate ?? Carbon::today())->copy();
        $weekStart  = $reference->copy()->subWeek()->startOfWeek(Carbon::MONDAY);
        $weekEnd    = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
        $thursday   = $weekStart->copy()->addDays(3);
        $monthStart = $thursday->copy()->startOfMonth();
        $monthEnd   = $thursday->copy()->endOfMonth();

        $budgets = $this->reportRepo->findAllActiveMonthlyBudgets();
        $count   = 0;

        foreach ($budgets as $budget) {
            $computed = $this->computeWeekSnapshot($budget, $weekStart, $weekEnd, $monthStart, $monthEnd);
            $this->reportRepo->upsertSnapshot([
                'user_id'          => $budget->user_id,
                'category_id'      => $budget->category_id,
                'budget_id'        => $budget->budget_id,
                'week_start'       => $weekStart->toDateString(),
                'week_end'         => $weekEnd->toDateString(),
                'allocated_amount' => $computed['allocated_amount'],
                'spent_amount'     => $computed['spent_amount'],
                'status'           => $computed['status'],
            ]);
            $count++;
        }

        return $count;
    }

    private function enumerateWeeksForMonth(Carbon $monthStart, Carbon $monthEnd): array
    {
        $weeks  = [];
        $cursor = $monthStart->copy()->startOfWeek(Carbon::MONDAY);

        while ($cursor->lte($monthEnd)) {
            $weekStart = $cursor->copy();
            $weekEnd   = $weekStart->copy()->endOfWeek(Carbon::SUNDAY);
            $thursday  = $weekStart->copy()->addDays(3);

            if ($thursday->month === $monthStart->month && $thursday->year === $monthStart->year) {
                $weeks[] = [$weekStart, $weekEnd];
            }
            $cursor->addWeek();
        }

        return $weeks;
    }

    private function overlapDays(Carbon $rangeStart, Carbon $rangeEnd, Carbon $boundStart, Carbon $boundEnd): int
    {
        $start = $rangeStart->gt($boundStart) ? $rangeStart : $boundStart;
        $end   = $rangeEnd->lt($boundEnd) ? $rangeEnd : $boundEnd;

        return $start->gt($end) ? 0 : (int) round($start->diffInDays($end)) + 1;
    }

    private function sumExpenses(int $userId, int $categoryId, Carbon $start, Carbon $end): float
    {
        if ($start->gt($end)) return 0.0;

        return (float) Transaction::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('transactionType_id', $this->expenseTypeId())
            ->whereBetween('transaction_date', [$start->toDateString(), $end->toDateString()])
            ->sum('total_amount');
    }

    private function expenseTypeId(): int
    {
        if ($this->expenseTypeId === null) {
            $this->expenseTypeId = (int) TransactionType::where('name', 'expense')->value('transactionType_id');
        }
        return $this->expenseTypeId;
    }
}
