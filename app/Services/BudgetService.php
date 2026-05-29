<?php

namespace App\Services;

use App\Models\Budget;
use App\Models\BudgetReminderSetting;
use App\Repositories\BudgetRepository;
use App\Repositories\ReminderRepository;
use App\Strategies\Reminder\ReminderChannelInterface;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class BudgetService
{
    /** @param ReminderChannelInterface[] $channels */
    public function __construct(
        private BudgetRepository $budgetRepo,
        private ReminderRepository $reminderRepo,
        private array $channels
    ) {}

    public function createOrUpdate(int $userId, array $data): Budget
    {
        return $this->budgetRepo->upsert($userId, $data);
    }

    public function getAll(int $userId): Collection
    {
        return $this->budgetRepo->findAllByUser($userId);
    }

    public function delete(int $budgetId, int $userId): bool
    {
        return $this->budgetRepo->delete($budgetId, $userId);
    }

    public function checkAndNotify(int $userId, int $categoryId): void
    {
        $budget = $this->budgetRepo->findActiveByUserAndCategory($userId, $categoryId);
        if (!$budget) {
            return;
        }

        $setting = $this->budgetRepo->getBudgetReminderSetting($userId);
        $threshold = $setting ? $setting->threshold : 80;
        $selectedChannels = $setting ? $setting->channels : ['popup'];

        $usagePercent = $budget->getUsagePercentage();
        if ($usagePercent < $threshold) {
            return;
        }

        $today = Carbon::today()->toDateString();
        $categoryName = $budget->category->category_name ?? "Kategori #{$categoryId}";
        $spent = $budget->getCurrentSpending();
        $limit = (float) $budget->amount;

        foreach ($selectedChannels as $channelKey) {
            if ($this->reminderRepo->isBudgetAlreadySent($categoryId, $today, $channelKey)) {
                continue;
            }

            $channel = $this->channels[$channelKey] ?? null;
            if (!$channel) {
                continue;
            }

            try {
                $channel->sendBudgetAlert($userId, $categoryName, $usagePercent, $spent, $limit, $categoryId);

                if ($channelKey !== 'popup') {
                    $this->reminderRepo->logBudgetSent($categoryId, $today, $channelKey);
                }
            } catch (\Exception $e) {
                Log::error("BudgetService alert gagal [{$channelKey}] user#{$userId} cat#{$categoryId}: " . $e->getMessage());
            }
        }
    }

    public function saveBudgetReminderSetting(int $userId, array $data): BudgetReminderSetting
    {
        return $this->budgetRepo->upsertBudgetReminderSetting($userId, [
            'channels'  => $data['channels'] ?? ['popup'],
            'threshold' => (int) ($data['threshold'] ?? 80),
        ]);
    }

    public function getBudgetReminderSetting(int $userId): ?BudgetReminderSetting
    {
        return $this->budgetRepo->getBudgetReminderSetting($userId);
    }

    public function buildAlertMessage(string $categoryName, float $usagePercent, float $spent, float $limit): string
    {
        $spentFmt = 'Rp ' . number_format($spent, 0, ',', '.');
        $limitFmt = 'Rp ' . number_format($limit, 0, ',', '.');
        return "Pengeluaran untuk kategori '{$categoryName}' sudah mencapai {$usagePercent}% dari budget {$spentFmt} dari {$limitFmt}.";
    }
}
