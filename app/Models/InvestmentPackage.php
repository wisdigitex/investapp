<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class InvestmentPackage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category',
        'min_amount',
        'max_amount',
        'duration_days',
        'roi_percent',
        'active',
    ];

    protected $casts = [
        'active' => 'boolean',
    ];

    public function userInvestments()
    {
        return $this->hasMany(UserInvestment::class, 'package_id');
    }
}
