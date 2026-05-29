<?php

namespace App\Strategies\Reminder;

use App\Models\RecurringTransaction;

interface ReminderChannelInterface
{
    public function send(RecurringTransaction $recurring, int $daysBefore, ?string $customMessage): void;

    public function sendBudgetAlert(int $userId, string $categoryName, float $usagePercent, float $spent, float $limit, int $categoryId): void;
}
