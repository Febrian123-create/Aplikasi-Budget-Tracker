<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BudgetReminderSetting extends Model
{
    protected $table = 'budget_reminder_settings';

    protected $fillable = [
        'user_id',
        'channels',
        'threshold',
    ];

    protected $casts = [
        'channels'  => 'array',
        'threshold' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
