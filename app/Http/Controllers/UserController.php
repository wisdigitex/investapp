<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPackage;
use App\Models\UserInvestment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class UserController extends Controller
{
    public function invest(Request $request)
    {
        $request->validate([
            'package_id' => 'required|exists:investment_packages,id',
            'amount' => 'required|numeric|min:1',
        ]);

        $user = auth()->user();

        $package = InvestmentPackage::find($request->package_id);

        // Check amount limits
        if ($request->amount < $package->min_amount || $request->amount > $package->max_amount) {
            return response()->json(['error' => 'Amount out of allowed range'], 422);
        }

        // Check user balance
        if ($user->main_balance < $request->amount) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        // Deduct balance
        $user->main_balance -= $request->amount;
        $user->save();

        // ROI calculations
        $roiTotal = ($request->amount * $package->roi_percent) / 100;
        $dailyProfit = $roiTotal / $package->duration_days;

        // Create Investment
        $investment = UserInvestment::create([
            'user_id' => $user->id,
            'package_id' => $package->id,
            'amount' => $request->amount,
            'expected_payout' => $request->amount + $roiTotal,
            'daily_profit' => $dailyProfit,
            'total_profit' => 0,
            'start_date' => Carbon::now(),
            'end_date' => Carbon::now()->addDays($package->duration_days),
            'status' => 'active',
        ]);

        return response()->json([
            'message' => 'Investment created successfully',
            'investment' => $investment
        ]);
    }
}
