<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Models\Transaction;

class ExpenseOverviewObserver implements ObserverInterface
{
    public function onUpdate(string $event, mixed $data): void
    {
        if ($event !== 'calculate') {
            return;
        }

        /** @var OverviewSubject $subject */
        $subject = $data;

        $totalExpense = Transaction::where('user_id', $subject->getUserId())
            ->whereBetween('transaction_date', [$subject->getStartDate(), $subject->getEndDate()])
            ->whereIn('transactionType_id', function ($query) {
                $query->select('transactionType_id')
                    ->from('transactiontype')
                    ->where('name', 'expense');
            })
            ->sum('total_amount');

        $subject->setTotalExpense($totalExpense);
    }
}
