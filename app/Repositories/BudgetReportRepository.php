<?php

namespace App\Repositories;

use App\Models\Budget;
use App\Models\WeeklyBudgetSnapshot;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BudgetReportRepository
{
    public function findActiveMonthlyBudgetsByUser(int $userId): Collection
    {
        return Budget::where('user_id', $userId)
            ->where('period', 'bulanan')
            ->with('category')
            ->get()
            ->filter(fn(Budget $b) => $b->isActive())
            ->values();
    }

    public function findAllActiveMonthlyBudgets(): Collection
    {
        return Budget::where('period', 'bulanan')
            ->with(['user', 'category'])
            ->get()
            ->filter(fn(Budget $b) => $b->isActive())
            ->values();
    }

    public function findSnapshot(int $userId, int $categoryId, string $weekStart): ?WeeklyBudgetSnapshot
    {
        return WeeklyBudgetSnapshot::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where('week_start', $weekStart)
            ->first();
    }

    public function upsertSnapshot(array $data): WeeklyBudgetSnapshot
    {
        return WeeklyBudgetSnapshot::updateOrCreate(
            ['user_id' => $data['user_id'], 'category_id' => $data['category_id'], 'week_start' => $data['week_start']],
            ['budget_id' => $data['budget_id'], 'week_end' => $data['week_end'],
             'allocated_amount' => $data['allocated_amount'], 'spent_amount' => $data['spent_amount'],
             'status' => $data['status']]
        );
    }
}
