<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $table = 'transaction';
    protected $primaryKey = 'transaction_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'category_id',
        'transactionType_id',
        'total_amount',
        'transaction_date',
        'description',
    ];

    public function getRouteKeyName()
    {
        return 'transaction_id';
    }
}
