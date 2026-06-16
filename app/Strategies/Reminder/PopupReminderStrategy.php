<?php

namespace App\Strategies\Reminder;

use App\Models\RecurringTransaction;
use App\Repositories\ReminderRepository;

class PopupReminderStrategy implements ReminderChannelInterface
{
    public function __construct(private ReminderRepository $reminderRepo) {}

    public function send(RecurringTransaction $recurring, int $daysBefore, ?string $customMessage): void
    {
        $this->reminderRepo->logSent(
            $recurring->recurring_id,
            $daysBefore,
            now()->toDateString(),
            'popup',
            $recurring->user_id
        );
    }

    public function sendBudgetAlert(int $userId, string $categoryName, float $usagePercent, float $spent, float $limit, int $categoryId): void
    {
        $this->reminderRepo->logBudgetSent(
            $categoryId,
            now()->toDateString(),
            'popup',
            $userId
        );
    }
}
