<?php

namespace App\Strategies\Reminder;

use App\Mail\RecurringReminderMail;
use App\Mail\BudgetAlertMail;
use App\Models\RecurringTransaction;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class EmailReminderStrategy implements ReminderChannelInterface
{
    public function send(RecurringTransaction $recurring, int $daysBefore, ?string $customMessage): void
    {
        $user = User::find($recurring->user_id);
        if (!$user) {
            return;
        }

        try {
            Mail::to($user->email)->queue(new RecurringReminderMail($recurring, $daysBefore, $customMessage));
        } catch (\Exception $e) {
            Log::error('EmailReminderStrategy gagal kirim ke ' . $user->email . ': ' . $e->getMessage());
        }
    }

    public function sendBudgetAlert(int $userId, string $categoryName, float $usagePercent, float $spent, float $limit, int $categoryId): void
    {
        $user = User::find($userId);
        if (!$user) {
            return;
        }

        try {
            Mail::to($user->email)->queue(new BudgetAlertMail($user, $categoryName, $usagePercent, $spent, $limit));
        } catch (\Exception $e) {
            Log::error('EmailReminderStrategy budget alert gagal: ' . $e->getMessage());
        }
    }
}
