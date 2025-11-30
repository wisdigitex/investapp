<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UserInvestment;
use App\Models\Transaction;
use Carbon\Carbon;

class ProcessInvestmentROI extends Command
{
    protected $signature = 'investments:roi';
    protected $description = 'Process daily ROI for all active user investments';

    public function handle()
    {
        $this->info("Processing daily ROI...");

        $today = Carbon::now()->startOfDay();

        // Fetch all active investments
        $investments = UserInvestment::where('status', 'active')->get();

        foreach ($investments as $inv) {

            // Skip if ROI already paid today
            if ($inv->last_paid_at && Carbon::parse($inv->last_paid_at)->isSameDay($today)) {
                continue;
            }

            // If investment matured → mark completed
            if (Carbon::now()->greaterThanOrEqualTo($inv->end_date)) {
                $inv->status = 'completed';
                $inv->save();
                continue;
            }

            // Pay daily profit
            $user = $inv->user;
            $profit = $inv->daily_profit;

            $inv->total_profit += $profit;
            $inv->last_paid_at = $today;
            $inv->save();

            // Credit to user balance
            $user->earnings_balance += $profit;
            $user->save();

            // Log transaction
            Transaction::create([
                'user_id' => $user->id,
                'type' => 'profit',
                'amount' => $profit,
                'status' => 'approved'
            ]);
        }

        $this->info("ROI processing completed.");
    }
}
