<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    protected $table = 'membership';
    protected $primaryKey = 'membership_id';
    public $timestamps = false;

    protected $fillable = ['membership_name'];

    public function users()
    {
        return $this->hasMany(User::class, 'membership_id', 'membership_id');
    }
}
