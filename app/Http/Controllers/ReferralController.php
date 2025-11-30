<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use Illuminate\Http\Request;

class ReferralController extends Controller
{
    public function stats(Request $request)
    {
        $user = $request->user();

        $refs = Referral::where('referrer_id', $user->id)
            ->with('referred')
            ->get();

        return [
            'referral_code' => $user->referral_code,
            'referral_count' => $refs->count(),
            'total_earnings' => $refs->sum('bonus_amount'),
            'referrals' => $refs
        ];
    }
}
