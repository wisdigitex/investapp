<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    // -------------------------------------------
    // REQUEST WITHDRAWAL
    // -------------------------------------------
    public function requestWithdraw(Request $request)
    {
        $user = $request->user();

        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'coin'           => 'required|string',
            'wallet_address' => 'required|string',
        ]);

        // Check user profile level limits
        $level = $user->profileLevel;

        if ($request->amount < $level->min_withdrawal) {
            return response()->json(['message' => 'Amount below minimum withdrawal'], 422);
        }

        if ($user->earnings_balance < $request->amount) {
            return response()->json(['message' => 'Insufficient earnings balance'], 422);
        }

        // Daily withdrawal limit
        $todayWithdrawals = Transaction::where('user_id', $user->id)
            ->where('type', 'withdrawal')
            ->whereDate('created_at', Carbon::today())
            ->count();

        if ($todayWithdrawals >= $level->max_daily_withdrawals) {
            return response()->json(['message' => 'Daily withdrawal limit reached'], 422);
        }

        // Deduct balance
        $user->update([
            'earnings_balance' => $user->earnings_balance - $request->amount
        ]);

        // Create withdrawal (pending)
        $withdraw = Transaction::create([
            'user_id'       => $user->id,
            'type'          => 'withdrawal',
            'direction'     => 'debit',
            'amount'        => $request->amount,
            'currency'      => $request->coin,
            'wallet_address'=> $request->wallet_address,
            'status'        => 'pending',
        ]);

        return response()->json([
            'status' => 'pending',
            'message' => 'Withdrawal request submitted',
            'withdrawal' => $withdraw
        ]);
    }

    // -------------------------------------------
    // WITHDRAWAL HISTORY
    // -------------------------------------------
    public function history(Request $request)
    {
        return Transaction::where('user_id', $request->user()->id)
            ->where('type', 'withdrawal')
            ->latest()
            ->get();
    }
}

$level = $user->profileLevel;

// Minimum withdrawal
if ($request->amount < $level->min_withdrawal) {
    return response()->json(['error' => 'Below minimum withdrawal limit'], 422);
}

// Daily withdrawal limit
$todayWithdrawals = Transaction::where('user_id', $user->id)
    ->where('type', 'withdrawal')
    ->whereDate('created_at', today())
    ->count();

if ($todayWithdrawals >= $level->max_daily_withdrawals) {
    return response()->json(['error' => 'Daily withdrawal limit reached'], 422);
}

// Referral requirement
if ($level->requires_referrals) {
    $refCount = Referral::where('referrer_id', $user->id)->count();
    if ($refCount < $level->min_referrals) {
        return response()->json(['error' => 'Not enough referrals'], 422);
    }
}
