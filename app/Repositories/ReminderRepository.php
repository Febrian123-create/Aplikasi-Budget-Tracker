<?php

namespace App\Repositories;

use App\Models\Reminder;
use App\Models\ReminderLog;
use App\Models\RecurringTransaction;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class ReminderRepository
{
    public function findByRecurringId(int $recurringId): ?Reminder
    {
        return Reminder::where('recurring_id', $recurringId)->first();
    }

    public function createOrUpdate(int $recurringId, int $userId, array $data): Reminder
    {
        return Reminder::updateOrCreate(
            ['recurring_id' => $recurringId],
            array_merge($data, ['user_id' => $userId])
        );
    }

    public function deleteByRecurringId(int $recurringId): void
    {
        Reminder::where('recurring_id', $recurringId)->delete();
    }

    public function getActiveRemindersForDate(string $date): Collection
    {
        return Reminder::where('reminder_enabled', true)
            ->with(['recurringTransaction' => function ($q) {
                $q->where('status', 'aktif')->with('user', 'category');
            }])
            ->get()
            ->filter(function (Reminder $reminder) use ($date) {
                $recurring = $reminder->recurringTransaction;
                if (!$recurring || $recurring->status !== 'aktif') {
                    return false;
                }
                $nextRun = Carbon::parse($recurring->next_run_date);
                foreach ($reminder->reminder_days as $daysBefore) {
                    $triggerDate = $nextRun->copy()->subDays((int) $daysBefore)->toDateString();
                    if ($triggerDate === $date) {
                        return true;
                    }
                }
                return false;
            });
    }

    public function isAlreadySent(int $recurringId, int $daysBefore, string $scheduledDate, string $channel): bool
    {
        return ReminderLog::where('recurring_id', $recurringId)
            ->where('days_before', $daysBefore)
            ->where('scheduled_date', $scheduledDate)
            ->where('channel', $channel)
            ->where('type', 'recurring')
            ->exists();
    }

    public function logSent(int $recurringId, int $daysBefore, string $scheduledDate, string $channel, ?int $userId = null): ReminderLog
    {
        $userId ??= RecurringTransaction::where('recurring_id', $recurringId)->value('user_id');

        return ReminderLog::create([
            'recurring_id'   => $recurringId,
            'user_id'        => $userId,
            'days_before'    => $daysBefore,
            'scheduled_date' => $scheduledDate,
            'channel'        => $channel,
            'type'           => 'recurring',
            'sent_at'        => now(),
            'is_read'        => false,
        ]);
    }

    public function isBudgetAlreadySent(int $categoryId, string $scheduledDate, string $channel, ?int $userId = null): bool
    {
        return ReminderLog::where('category_id', $categoryId)
            ->where('scheduled_date', $scheduledDate)
            ->where('channel', $channel)
            ->where('type', 'budget')
            ->when($userId !== null, fn($q) => $q->where('user_id', $userId))
            ->exists();
    }

    public function logBudgetSent(int $categoryId, string $scheduledDate, string $channel, ?int $userId = null): ReminderLog
    {
        return ReminderLog::create([
            'recurring_id'   => null,
            'user_id'        => $userId,
            'category_id'    => $categoryId,
            'days_before'    => null,
            'scheduled_date' => $scheduledDate,
            'channel'        => $channel,
            'type'           => 'budget',
            'sent_at'        => now(),
            'is_read'        => false,
        ]);
    }

    public function getUnreadPopups(int $userId): Collection
    {
        return ReminderLog::where('is_read', false)
            ->where('channel', 'popup')
            ->where('user_id', $userId)
            ->orderByDesc('created_at')
            ->get();
    }

    public function markPopupAsRead(int $logId): void
    {
        ReminderLog::where('id', $logId)->update(['is_read' => true]);
    }

    public function markAllPopupsAsRead(int $userId): void
    {
        ReminderLog::where('is_read', false)
            ->where('channel', 'popup')
            ->where('user_id', $userId)
            ->update(['is_read' => true]);
    }
}
