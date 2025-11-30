<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;

class UserInvestment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'package_id',
        'amount',
        'expected_payout',
        'start_date',
        'end_date',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(InvestmentPackage::class, 'package_id');
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function hasEnded()
    {
        return $this->end_date && $this->end_date->isPast();
    }
}
