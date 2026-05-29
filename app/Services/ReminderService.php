<?php

namespace App\Services;

use App\Models\Reminder;
use App\Models\RecurringTransaction;
use App\Repositories\ReminderRepository;
use App\Strategies\Reminder\ReminderChannelInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ReminderService
{
    /** @param ReminderChannelInterface[] $channels */
    public function __construct(
        private ReminderRepository $reminderRepo,
        private array $channels
    ) {}

    public function saveReminderConfig(RecurringTransaction $recurring, array $data): Reminder
    {
        return $this->reminderRepo->createOrUpdate(
            $recurring->recurring_id,
            $recurring->user_id,
            [
                'reminder_days'    => $data['reminder_days'] ?? [],
                'reminder_enabled' => (bool) ($data['reminder_enabled'] ?? false),
                'channels'         => $data['reminder_channels'] ?? ['popup'],
                'custom_message'   => $data['reminder_message'] ?? null,
            ]
        );
    }

    public function deleteReminderConfig(int $recurringId): void
    {
        $this->reminderRepo->deleteByRecurringId($recurringId);
    }

    public function processDueReminders(): int
    {
        $today = Carbon::today()->toDateString();
        $activeReminders = $this->reminderRepo->getActiveRemindersForDate($today);
        $sent = 0;

        foreach ($activeReminders as $reminder) {
            $recurring = $reminder->recurringTransaction;
            if (!$recurring) {
                continue;
            }

            $nextRun = Carbon::parse($recurring->next_run_date);

            foreach ($reminder->reminder_days as $daysBefore) {
                $triggerDate = $nextRun->copy()->subDays((int) $daysBefore)->toDateString();
                if ($triggerDate !== $today) {
                    continue;
                }

                foreach ($reminder->channels as $channelKey) {
                    $channel = $this->resolveChannel($channelKey);
                    if (!$channel) {
                        continue;
                    }

                    if ($this->reminderRepo->isAlreadySent($recurring->recurring_id, $daysBefore, $today, $channelKey)) {
                        continue;
                    }

                    try {
                        $channel->send($recurring, (int) $daysBefore, $reminder->custom_message);

                        if ($channelKey !== 'popup') {
                            $this->reminderRepo->logSent($recurring->recurring_id, $daysBefore, $today, $channelKey);
                        }

                        $sent++;
                    } catch (\Exception $e) {
                        Log::error("ReminderService gagal kirim [{$channelKey}] recurring #{$recurring->recurring_id}: " . $e->getMessage());
                    }
                }
            }
        }

        return $sent;
    }

    public function getUnreadPopups(int $userId): Collection
    {
        return $this->reminderRepo->getUnreadPopups($userId);
    }

    public function markPopupAsRead(int $logId): void
    {
        $this->reminderRepo->markPopupAsRead($logId);
    }

    private function resolveChannel(string $key): ?ReminderChannelInterface
    {
        return $this->channels[$key] ?? null;
    }

    private function buildDefaultMessage(RecurringTransaction $recurring, int $daysBefore): string
    {
        $label = $daysBefore === 0 ? 'hari ini' : "{$daysBefore} hari lagi";
        $amount = 'Rp ' . number_format($recurring->amount, 0, ',', '.');
        return "Transaksi rutin '{$recurring->description}' sebesar {$amount} akan dieksekusi {$label}.";
    }
}
