<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Services\BudgetService;

class BudgetObserver implements ObserverInterface
{
    public function __construct(private BudgetService $budgetService) {}

    public function onUpdate(string $event, mixed $data): void
    {
        if (!in_array($event, ['created', 'updated'])) {
            return;
        }

        // Hanya cek saat expense ditambahkan/diubah
        if (!$data->transactionType || $data->transactionType->name !== 'expense') {
            return;
        }

        $this->budgetService->checkAndNotify($data->user_id, $data->category_id);
    }
}
