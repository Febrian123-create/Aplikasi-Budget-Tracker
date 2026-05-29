<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $table = 'reminders';
    protected $primaryKey = 'reminder_id';

    protected $fillable = [
        'recurring_id',
        'user_id',
        'reminder_days',
        'reminder_enabled',
        'channels',
        'custom_message',
    ];

    protected $casts = [
        'reminder_days'    => 'array',
        'channels'         => 'array',
        'reminder_enabled' => 'boolean',
    ];

    public function recurringTransaction()
    {
        return $this->belongsTo(RecurringTransaction::class, 'recurring_id', 'recurring_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function logs()
    {
        return $this->hasMany(ReminderLog::class, 'recurring_id', 'recurring_id');
    }

    public function isEnabled(): bool
    {
        return $this->reminder_enabled;
    }
}
