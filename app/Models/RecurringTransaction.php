<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class RecurringTransaction extends Model
{
    use HasFactory;

    protected $table = 'recurring_transaction';
    protected $primaryKey = 'recurring_id';
    protected $keyType = 'int';
    public $incrementing = true;

    protected $fillable = [
        'user_id',
        'category_id',
        'amount',
        'frequency',
        'start_date',
        'end_date',
        'next_run_date',
        'reminder_id',
        'description',
        'amount_type',
        'status',
    ];

    protected $casts = [
        'start_date'    => 'date',
        'end_date'      => 'date',
        'next_run_date' => 'date',
        'amount'        => 'decimal:2',
    ];

    public function getRouteKeyName(): string
    {
        return 'recurring_id';
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id', 'category_id');
    }

    
    public function scopeForUser($query, int $userId)
    {
        return $query->where('user_id', $userId);
    }

    
    public function scopeActive($query)
    {
        return $query->where('status', 'aktif');
    }

    
    public function scopeDueToday($query)
    {
        return $query->where('next_run_date', '<=', now()->toDateString());
    }
}
