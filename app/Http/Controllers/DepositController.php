<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    // -------------------------------------------
    // CREATE DEPOSIT REQUEST
    // -------------------------------------------
    public function create(Request $request)
    {
        $request->validate([
            'amount'         => 'required|numeric|min:1',
            'coin'           => 'required|string',
            'tx_hash'        => 'nullable|string',
            'wallet_address' => 'required|string',
        ]);

        $deposit = Transaction::create([
            'user_id'       => $request->user()->id,
            'type'          => 'deposit',
            'direction'     => 'credit',
            'amount'        => $request->amount,
            'currency'      => $request->coin,
            'wallet_address'=> $request->wallet_address,
            'tx_hash'       => $request->tx_hash,
            'status'        => 'pending',
        ]);

        return response()->json([
            'status' => 'pending',
            'message' => 'Deposit awaiting admin approval',
            'deposit' => $deposit
        ]);
    }

    // -------------------------------------------
    // USER DEPOSIT HISTORY
    // -------------------------------------------
    public function history(Request $request)
    {
        return Transaction::where('user_id', $request->user()->id)
            ->where('type', 'deposit')
            ->latest()
            ->get();
    }

    public function createDeposit(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|string',
            'tx_hash' => 'required|string',
            'screenshot' => 'nullable|image|max:2048',
        ]);

        $user = auth()->user();

        // store image
        $imagePath = null;
        if ($request->hasFile('screenshot')) {
            $imagePath = $request->file('screenshot')->store('deposit_proofs', 'public');
        }

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'deposit',
            'amount' => $request->amount,
            'payment_method' => $request->payment_method,
            'wallet_address' => Setting::first()->{$request->payment_method . '_wallet'},
            'tx_hash' => $request->tx_hash,
            'screenshot' => $imagePath,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Deposit submitted and pending admin approval.',
            'transaction' => $transaction
        ]);
    }

    public function requestWithdrawal(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
            'wallet_address' => 'required|string'
        ]);

        $user = auth()->user();

        // check balance
        if ($request->amount > $user->earnings_balance) {
            return response()->json(['error' => 'Insufficient balance'], 422);
        }

        // create transaction
        $transaction = Transaction::create([
            'user_id' => $user->id,
            'type' => 'withdrawal',
            'amount' => $request->amount,
            'wallet_address' => $request->wallet_address,
            'status' => 'pending',
        ]);

        return response()->json([
            'message' => 'Withdrawal request submitted.',
            'transaction' => $transaction
        ]);
    }

}
