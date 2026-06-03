<?php

namespace App\Strategies\Reminder;

use App\Models\RecurringTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class GoogleCalendarReminderStrategy implements ReminderChannelInterface
{
    public function send(RecurringTransaction $recurring, int $daysBefore, ?string $customMessage): void
    {
        $user = User::find($recurring->user_id);
        if (!$user || !$this->hasGoogleToken($user)) {
            Log::info('GoogleCalendarReminderStrategy: user ' . $recurring->user_id . ' belum connect Google Calendar. Skip.');
            return;
        }

        try {
            $client = $this->buildClient($user->google_calendar_token);
            $service = new \Google\Service\Calendar($client);

            $label = $daysBefore === 0 ? 'Hari Ini' : "H-{$daysBefore}";
            $summary = "[BUNREK] {$label}: {$recurring->description}";
            $description = $customMessage ?? $this->buildDefaultMessage($recurring, $daysBefore);

            $event = new \Google\Service\Calendar\Event([
                'summary'     => $summary,
                'description' => $description,
                'start'       => ['date' => $recurring->next_run_date->toDateString()],
                'end'         => ['date' => $recurring->next_run_date->toDateString()],
            ]);

            $service->events->insert('primary', $event);
        } catch (\Exception $e) {
            Log::error('GoogleCalendarReminderStrategy gagal: ' . $e->getMessage());
        }
    }

    public function sendBudgetAlert(int $userId, string $categoryName, float $usagePercent, float $spent, float $limit, int $categoryId): void
    {
        $user = User::find($userId);
        if (!$user || !$this->hasGoogleToken($user)) {
            return;
        }

        try {
            $client = $this->buildClient($user->google_calendar_token);
            $service = new \Google\Service\Calendar($client);

            $event = new \Google\Service\Calendar\Event([
                'summary'     => "[BUNREK] Peringatan Budget: {$categoryName}",
                'description' => "Pengeluaran untuk {$categoryName} sudah mencapai {$usagePercent}% dari budget (Rp " . number_format($spent, 0, ',', '.') . " dari Rp " . number_format($limit, 0, ',', '.') . ").",
                'start'       => ['date' => now()->toDateString()],
                'end'         => ['date' => now()->toDateString()],
            ]);

            $service->events->insert('primary', $event);
        } catch (\Exception $e) {
            Log::error('GoogleCalendarReminderStrategy budget gagal: ' . $e->getMessage());
        }
    }

    private function hasGoogleToken(User $user): bool
    {
        return !empty($user->google_calendar_token);
    }

    private function buildClient(string $token): \Google\Client
    {
        $client = new \Google\Client();
        $client->setAccessToken(json_decode($token, true));
        return $client;
    }

    private function buildDefaultMessage(RecurringTransaction $recurring, int $daysBefore): string
    {
        $label = $daysBefore === 0 ? 'hari ini' : "{$daysBefore} hari lagi";
        $amount = 'Rp ' . number_format($recurring->amount, 0, ',', '.');
        return "Transaksi rutin '{$recurring->description}' sebesar {$amount} akan dieksekusi {$label} pada {$recurring->next_run_date->format('d M Y')}.";
    }
}
