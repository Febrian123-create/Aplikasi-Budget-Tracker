<?php

namespace App\Repositories;

use App\Models\Budget;
use App\Models\BudgetReminderSetting;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class BudgetRepository
{
    public function findActiveByUserAndCategory(int $userId, int $categoryId): ?Budget
    {
        return Budget::where('user_id', $userId)
            ->where('category_id', $categoryId)
            ->where(function ($q) {
                $q->whereNull('end_date')->orWhere('end_date', '>=', Carbon::today());
            })
            ->latest('budget_id')
            ->first();
    }

    public function findAllByUser(int $userId): Collection
    {
        return Budget::where('user_id', $userId)
            ->with('category')
            ->orderBy('category_id')
            ->get();
    }

    public function findById(int $budgetId, int $userId): ?Budget
    {
        return Budget::where('budget_id', $budgetId)->where('user_id', $userId)->first();
    }

    public function upsert(int $userId, array $data): Budget
    {
        return Budget::updateOrCreate(
            [
                'user_id'     => $userId,
                'category_id' => $data['category_id'],
                'period'      => $data['period'],
            ],
            [
                'amount'     => $data['amount'],
                'duration'   => $data['duration'] ?? null,
                'start_date' => $data['start_date'],
                'end_date'   => $data['end_date'] ?? null,
            ]
        );
    }

    public function delete(int $budgetId, int $userId): bool
    {
        $budget = $this->findById($budgetId, $userId);
        if (!$budget) {
            return false;
        }
        return $budget->delete();
    }

    public function getBudgetReminderSetting(int $userId): ?BudgetReminderSetting
    {
        return BudgetReminderSetting::where('user_id', $userId)->first();
    }

    public function upsertBudgetReminderSetting(int $userId, array $data): BudgetReminderSetting
    {
        return BudgetReminderSetting::updateOrCreate(
            ['user_id' => $userId],
            $data
        );
    }
}
