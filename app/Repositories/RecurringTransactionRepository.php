<?php

namespace App\Repositories;

use App\Models\RecurringTransaction;
use Illuminate\Support\Collection;
use Carbon\Carbon;


class RecurringTransactionRepository
{
    
    public function findAll(int $userId): Collection
    {
        return RecurringTransaction::forUser($userId)
            ->with('category')
            ->orderBy('next_run_date', 'asc')
            ->get();
    }

    
    public function findById(int $recurringId): ?RecurringTransaction
    {
        return RecurringTransaction::with('category')->find($recurringId);
    }

    
    public function findDueToday(int $userId): Collection
    {
        return RecurringTransaction::forUser($userId)
            ->active()
            ->dueToday()
            ->with('category')
            ->get();
    }

    
    public function findAllDueToday(): Collection
    {
        return RecurringTransaction::active()
            ->dueToday()
            ->with('category')
            ->get();
    }

    
    public function save(RecurringTransaction $recurring): RecurringTransaction
    {
        $recurring->save();
        return $recurring;
    }

    
    public function update(RecurringTransaction $recurring, array $data): RecurringTransaction
    {
        $recurring->update($data);
        return $recurring->fresh('category');
    }

    
    public function delete(RecurringTransaction $recurring): bool
    {
        return $recurring->delete();
    }

    
    public function countActive(int $userId): int
    {
        return RecurringTransaction::forUser($userId)
            ->active()
            ->count();
    }

    
    public function findByIdAndUser(int $recurringId, int $userId): ?RecurringTransaction
    {
        return RecurringTransaction::forUser($userId)
            ->with('category')
            ->find($recurringId);
    }
}
