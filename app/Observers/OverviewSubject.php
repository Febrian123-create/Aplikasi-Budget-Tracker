<?php

namespace App\Observers;

use App\Contracts\ObserverInterface;
use App\Contracts\SubjectInterface;
use App\Models\Transaction;
use Carbon\Carbon;

class OverviewSubject implements SubjectInterface
{
    private array $observers = [];

    private string $type;
    private ?int $year;
    private ?int $month;
    private ?int $week;
    private int $userId;

    private string $startDate;
    private string $endDate;
    private float $totalIncome = 0;
    private float $totalExpense = 0;

    public function subscribe(ObserverInterface $observer): void
    {
        $key = spl_object_id($observer);
        $this->observers[$key] = $observer;
    }

    public function unsubscribe(ObserverInterface $observer): void
    {
        $key = spl_object_id($observer);
        unset($this->observers[$key]);
    }

    public function notifyObservers(string $event, mixed $data): void
    {
        foreach ($this->observers as $observer) {
            $observer->onUpdate($event, $data);
        }
    }

    public function setFilter(string $type, ?int $year, ?int $month, ?int $week, int $userId): void
    {
        $this->type = $type;
        $this->year = $year;
        $this->month = $month;
        $this->week = $week;
        $this->userId = $userId;

        $this->calculateDates();
        $this->notifyObservers('calculate', $this);
    }

    private function calculateDates(): void
    {
        $year = $this->year ?? Carbon::today()->year;
        $month = $this->month ?? Carbon::today()->month;

        switch ($this->type) {
            case 'mingguan':
                $week = $this->week ?? 1;
                $daysInMonth = Carbon::create($year, $month, 1)->daysInMonth;
                if ($week === 1) {
                    $start = Carbon::create($year, $month, 1)->startOfDay();
                    $end = Carbon::create($year, $month, 7)->endOfDay();
                } elseif ($week === 2) {
                    $start = Carbon::create($year, $month, 8)->startOfDay();
                    $end = Carbon::create($year, $month, 14)->endOfDay();
                } elseif ($week === 3) {
                    $start = Carbon::create($year, $month, 15)->startOfDay();
                    $end = Carbon::create($year, $month, 21)->endOfDay();
                } elseif ($week === 4) {
                    $start = Carbon::create($year, $month, 22)->startOfDay();
                    $end = Carbon::create($year, $month, 28)->endOfDay();
                } else {
                    $start = Carbon::create($year, $month, 29)->startOfDay();
                    $end = Carbon::create($year, $month)->endOfMonth()->endOfDay();
                }
                break;

            case 'tahunan':
                $start = Carbon::create($year, 1, 1)->startOfYear();
                $end = Carbon::create($year, 12, 31)->endOfYear();
                break;

            case 'keseluruhan':
                $firstTx = Transaction::where('user_id', $this->userId)
                    ->orderBy('transaction_date', 'asc')
                    ->first();
                $start = $firstTx 
                    ? Carbon::parse($firstTx->transaction_date)->startOfDay()
                    : Carbon::today()->subYear()->startOfDay();
                $end = Carbon::today()->endOfDay();
                break;

            case 'bulanan':
            default:
                $start = Carbon::create($year, $month, 1)->startOfMonth();
                $end = Carbon::create($year, $month, 1)->endOfMonth();
                break;
        }

        $this->startDate = $start->toDateString();
        $this->endDate = $end->toDateString();
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getEndDate(): string
    {
        return $this->endDate;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function getMonth(): ?int
    {
        return $this->month;
    }

    public function getWeek(): ?int
    {
        return $this->week;
    }

    public function getTotalIncome(): float
    {
        return $this->totalIncome;
    }

    public function setTotalIncome(float $totalIncome): void
    {
        $this->totalIncome = $totalIncome;
    }

    public function getTotalExpense(): float
    {
        return $this->totalExpense;
    }

    public function setTotalExpense(float $totalExpense): void
    {
        $this->totalExpense = $totalExpense;
    }

    public function getBalance(): float
    {
        return $this->totalIncome - $this->totalExpense;
    }
}
