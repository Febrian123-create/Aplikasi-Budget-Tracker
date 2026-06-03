<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ReminderLog extends Model
{
    protected $table = 'reminder_logs';

    protected $fillable = [
        'recurring_id',
        'category_id',
        'days_before',
        'scheduled_date',
        'channel',
        'type',
        'sent_at',
        'is_read',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'sent_at'        => 'datetime',
        'is_read'        => 'boolean',
    ];

    public function recurringTransaction()
    {
        return $this->belongsTo(RecurringTransaction::class, 'recurring_id', 'recurring_id');
    }

    public function scopeUnread($query, int $userId)
    {
        return $query->where('is_read', false)
            ->where(function ($q) use ($userId) {
                $q->whereHas('recurringTransaction', fn($r) => $r->where('user_id', $userId))
                  ->orWhereNull('recurring_id');
            });
    }
}
