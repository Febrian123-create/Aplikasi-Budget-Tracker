<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeeklyBudgetSnapshot extends Model
{
    protected $table = 'weekly_budget_snapshots';
    protected $primaryKey = 'snapshot_id';

    protected $fillable = [
        'user_id', 'category_id', 'budget_id',
        'week_start', 'week_end',
        'allocated_amount', 'spent_amount', 'status',
    ];

    protected $casts = [
        'week_start'       => 'date',
        'week_end'         => 'date',
        'allocated_amount' => 'decimal:2',
        'spent_amount'     => 'decimal:2',
    ];

    public function user()    { return $this->belongsTo(User::class, 'user_id', 'id'); }
    public function category(){ return $this->belongsTo(Category::class, 'category_id', 'category_id'); }
    public function budget()  { return $this->belongsTo(Budget::class, 'budget_id', 'budget_id'); }

    public function isOverBudget(): bool { return $this->status === 'overbudget'; }

    public function weekLabel(): string
    {
        return $this->week_start->format('d M') . ' - ' . $this->week_end->format('d M Y');
    }
}
