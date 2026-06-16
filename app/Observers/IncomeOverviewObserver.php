<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Models\Transaction;

class IncomeOverviewObserver implements ObserverInterface
{
    public function onUpdate(string $event, mixed $data): void
    {
        if ($event !== 'calculate') {
            return;
        }

        /** @var OverviewSubject $subject */
        $subject = $data;

        $totalIncome = Transaction::where('user_id', $subject->getUserId())
            ->whereBetween('transaction_date', [$subject->getStartDate(), $subject->getEndDate()])
            ->whereIn('transactionType_id', function ($query) {
                $query->select('transactionType_id')
                    ->from('transactiontype')
                    ->where('name', 'income');
            })
            ->sum('total_amount');

        $subject->setTotalIncome($totalIncome);
    }
}
