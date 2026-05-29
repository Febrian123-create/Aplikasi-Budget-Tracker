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

    /**
     * Ambil semua reminder aktif yang perlu dikirim pada tanggal tertentu.
     * Cek berdasarkan next_run_date - days_before = $date.
     */
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

    public function logSent(int $recurringId, int $daysBefore, string $scheduledDate, string $channel): ReminderLog
    {
        return ReminderLog::create([
            'recurring_id'   => $recurringId,
            'days_before'    => $daysBefore,
            'scheduled_date' => $scheduledDate,
            'channel'        => $channel,
            'type'           => 'recurring',
            'sent_at'        => now(),
            'is_read'        => false,
        ]);
    }

    public function isBudgetAlreadySent(int $categoryId, string $scheduledDate, string $channel): bool
    {
        return ReminderLog::where('category_id', $categoryId)
            ->where('scheduled_date', $scheduledDate)
            ->where('channel', $channel)
            ->where('type', 'budget')
            ->exists();
    }

    public function logBudgetSent(int $categoryId, string $scheduledDate, string $channel): ReminderLog
    {
        return ReminderLog::create([
            'recurring_id'   => null,
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
        $recurringIds = RecurringTransaction::where('user_id', $userId)->pluck('recurring_id');

        return ReminderLog::where('is_read', false)
            ->where('channel', 'popup')
            ->where(function ($q) use ($recurringIds, $userId) {
                $q->whereIn('recurring_id', $recurringIds)
                  ->orWhere(function ($q2) use ($userId) {
                      $q2->where('type', 'budget')->whereNull('recurring_id');
                  });
            })
            ->orderByDesc('created_at')
            ->get();
    }

    public function markPopupAsRead(int $logId): void
    {
        ReminderLog::where('id', $logId)->update(['is_read' => true]);
    }

    public function markAllPopupsAsRead(int $userId): void
    {
        $recurringIds = RecurringTransaction::where('user_id', $userId)->pluck('recurring_id');

        ReminderLog::where('is_read', false)
            ->where('channel', 'popup')
            ->whereIn('recurring_id', $recurringIds)
            ->update(['is_read' => true]);
    }
}
