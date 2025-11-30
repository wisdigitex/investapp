<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'direction',
        'amount',
        'currency',
        'wallet_address',
        'tx_hash',
        'status',
        'admin_id',
        'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    // Owner of the transaction (user)
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Admin who approved/rejected
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}
