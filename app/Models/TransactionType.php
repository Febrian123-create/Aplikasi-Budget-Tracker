<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionType extends Model
{
    use HasFactory;

    protected $table = 'transactiontype';
    protected $primaryKey = 'transactionType_id';
    protected $keyType = 'int';
    public $incrementing = true;
    public $timestamps = false;

    protected $fillable = [
        'name',
    ];

    // Relasi ke Transaction
    public function transactions()
    {
        return $this->hasMany(Transaction::class, 'transactionType_id', 'transactionType_id');
    }
}
