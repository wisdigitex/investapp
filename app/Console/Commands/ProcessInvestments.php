<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserInvestment;
use App\Models\Transaction;
use Carbon\Carbon;

class ProcessInvestments extends Command
{
    protected $signature = 'investments:process';
    protected $description = 'Check and process completed investments, credit earnings, and update statuses.';

    public function handle()
    {
        $this->info("Processing investments...");

        $now = Carbon::now();

        // All investments ending today or earlier AND still not completed
        $investments = UserInvestment::where('status', 'ongoing')
            ->whereDate('end_date', '<=', $now)
            ->get();

        if ($investments->count() === 0) {
            $this->info("No investments matured today.");
            return;
        }

        foreach ($investments as $inv) {
            $user = $inv->user;

            // Credit expected payout to earnings balance
            $user->earnings_balance += $inv->expected_payout;
            $user->save();

            // Create profit transaction
            Transaction::create([
                'user_id'       => $user->id,
                'type'          => 'profit',
                'direction'     => 'credit',
                'amount'        => $inv->expected_payout,
                'currency'      => 'USDT',
                'status'        => 'approved',
                'meta'          => [
                    'investment_id' => $inv->id,
                    'package' => $inv->package->name,
                ],
            ]);

            // Mark investment as completed
            $inv->status = 'completed';
            $inv->save();

            $this->info("Processed investment ID: {$inv->id} for user {$user->id}");
        }

        $this->info("Investment processing complete.");
    }
}
