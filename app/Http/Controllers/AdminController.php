<?php

namespace App\Http\Controllers;

use App\Models\InvestmentPackage;
use App\Models\Setting;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    // -------------------------------------------
    // PENDING DEPOSITS
    // -------------------------------------------
    public function pendingDeposits()
    {
        return Transaction::where('type', 'deposit')
            ->where('status', 'pending')
            ->with('user')
            ->get();
    }

    // APPROVE DEPOSIT
    public function approveDeposit(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
        ]);

        $tx = Transaction::find($request->transaction_id);
        $user = $tx->user;

        // Add funds
        $user->update([
            'main_balance' => $user->main_balance + $tx->amount
        ]);

        $tx->update([
            'status' => 'approved',
            'admin_id' => $request->user()->id
        ]);

        return ['status' => 'success', 'message' => 'Deposit approved'];
    }

    // REJECT DEPOSIT
    public function rejectDeposit(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required|exists:transactions,id'
        ]);

        Transaction::find($request->transaction_id)
            ->update([
                'status' => 'rejected',
                'admin_id' => $request->user()->id
            ]);

        return ['status' => 'success', 'message' => 'Deposit rejected'];
    }

    // -------------------------------------------
    // PENDING WITHDRAWALS
    // -------------------------------------------
    public function pendingWithdrawals()
    {
        return Transaction::where('type', 'withdrawal')
            ->where('status', 'pending')
            ->with('user')
            ->get();
    }

    // APPROVE WITHDRAWAL
    public function approveWithdrawal(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required',
            'tx_hash' => 'required'
        ]);

        $tx = Transaction::find($request->transaction_id);

        $tx->update([
            'status' => 'approved',
            'tx_hash' => $request->tx_hash,
            'admin_id' => $request->user()->id
        ]);

        return ['status' => 'success', 'message' => 'Withdrawal approved'];
    }

    // REJECT WITHDRAWAL
    public function rejectWithdrawal(Request $request)
    {
        $request->validate([
            'transaction_id' => 'required'
        ]);

        $tx = Transaction::find($request->transaction_id);
        $user = $tx->user;

        // Refund to earnings balance
        $user->update([
            'earnings_balance' => $user->earnings_balance + $tx->amount
        ]);

        $tx->update([
            'status' => 'rejected',
            'admin_id' => $request->user()->id
        ]);

        return ['status' => 'success', 'message' => 'Withdrawal rejected'];
    }

    // -------------------------------------------
    // ADD FUNDS MANUALLY
    // -------------------------------------------
    public function addFunds(Request $request)
    {
        $request->validate([
            'user_id' => 'required',
            'amount'  => 'required|numeric'
        ]);

        $user = User::find($request->user_id);

        $user->update([
            'main_balance' => $user->main_balance + $request->amount
        ]);

        Transaction::create([
            'user_id' => $user->id,
            'type' => 'adjustment',
            'direction' => 'credit',
            'amount' => $request->amount,
            'status' => 'approved',
            'admin_id' => $request->user()->id
        ]);

        return ['status' => 'success', 'message' => 'Funds added'];
    }

    // -------------------------------------------
    // CREATE PACKAGE
    // -------------------------------------------
    public function createPackage(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'min_amount' => 'required|numeric',
            'max_amount' => 'required|numeric',
            'duration_days' => 'required|integer',
            'roi_percent' => 'required|numeric'
        ]);

        InvestmentPackage::create($request->all());

        return ['status' => 'success', 'message' => 'Package created'];
    }

    // -------------------------------------------
    // UPDATE SETTINGS
    // -------------------------------------------
    public function updateSettings(Request $request)
    {
        foreach ($request->all() as $key => $value) {
            Setting::setValue($key, $value);
        }

        return ['status' => 'success', 'message' => 'Settings updated'];
    }
}
