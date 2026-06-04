<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Budget extends Model
{
    protected $table = 'budgets';
    protected $primaryKey = 'budget_id';

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'period',
        'duration',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'amount'     => 'decimal:2',
        'duration'   => 'integer',
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    public function getCurrentSpending(): float
    {
        $expenseTypeId = \App\Models\TransactionType::where('name', 'expense')->value('transactionType_id');

        return (float) Transaction::where('user_id', $this->user_id)
            ->where('category_id', $this->category_id)
            ->where('transactionType_id', $expenseTypeId)
            ->whereBetween('transaction_date', [$this->getPeriodStart(), $this->getPeriodEnd()])
            ->sum('total_amount');
    }

    public function getUsagePercentage(): float
    {
        if ($this->amount <= 0) {
            return 0;
        }

        return round(($this->getCurrentSpending() / (float) $this->amount) * 100, 1);
    }

    public function isExceeded(): bool
    {
        return $this->getCurrentSpending() >= (float) $this->amount;
    }

    public function isActive(): bool
    {
        return $this->end_date === null || $this->end_date->gte(Carbon::today());
    }

    public function periodLabel(): string
    {
        $periodName = ucfirst($this->period);

        if ($this->period === 'mingguan' || empty($this->duration)) {
            return $periodName;
        }

        $unit = $this->period === 'tahunan' ? 'tahun' : 'bulan';
        return "{$periodName} · berlaku {$this->duration} {$unit}";
    }

    private function getPeriodStart(): string
    {
        return match ($this->period) {
            // Mingguan memakai window tanggal yang diisi manual.
            'mingguan' => ($this->start_date ?? Carbon::now()->startOfWeek())->toDateString(),
            'tahunan'  => Carbon::now()->startOfYear()->toDateString(),
            default    => Carbon::now()->startOfMonth()->toDateString(),
        };
    }

    private function getPeriodEnd(): string
    {
        return match ($this->period) {
            'mingguan' => ($this->end_date ?? Carbon::now()->endOfWeek())->toDateString(),
            'tahunan'  => Carbon::now()->endOfYear()->toDateString(),
            default    => Carbon::now()->endOfMonth()->toDateString(),
        };
    }
}
