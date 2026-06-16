<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    protected $fillable = [
        'user_id',
        'nama',
        'target_harga',
        'terkumpul',
        'allocated_amount',
        'deadline',
        'status',
        'catatan',
    ];

    protected $casts = [
        'allocated_amount' => 'decimal:2',
    ];

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke WishlistAlokasi
     */
    public function alokasi()
    {
        return $this->hasMany(WishlistAlokasi::class, 'wishlist_id');
    }

    /**
     * Accessor: Hitung persentase terkumpul dari target_harga
     * Return integer 0-100
     */
    public function getProsesanAttribute()
    {
        if ($this->target_harga <= 0) {
            return 0;
        }

        $collected = $this->allocated_amount ?? $this->terkumpul;
        $persentase = ($collected / $this->target_harga) * 100;
        return min((int)$persentase, 100);
    }
}
