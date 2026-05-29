<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Services\ReminderService;

class ReminderObserver implements ObserverInterface
{
    public function __construct(private ReminderService $reminderService) {}

    public function onUpdate(string $event, mixed $data): void
    {
        if ($event !== 'recurring_deleted') {
            return;
        }

        $this->reminderService->deleteReminderConfig($data->recurring_id);
    }
}
