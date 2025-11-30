<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProfileLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'upgrade_price',
        'min_withdrawal',
        'max_daily_withdrawals',
        'requires_referrals',
        'min_referrals',
        'unlocks',
    ];

    protected $casts = [
        'requires_referrals' => 'boolean',
        'unlocks' => 'array',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
