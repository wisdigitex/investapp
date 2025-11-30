<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('user_investments', function (Blueprint $table) {

            if (!Schema::hasColumn('user_investments', 'daily_profit')) {
                $table->decimal('daily_profit', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('user_investments', 'total_profit')) {
                $table->decimal('total_profit', 10, 2)->default(0);
            }

            if (!Schema::hasColumn('user_investments', 'last_paid_at')) {
                $table->date('last_paid_at')->nullable();
            }

            // Change status enum from: ['ongoing','completed','cancelled']
            // → to: ['active','completed','cancelled']
            if (Schema::hasColumn('user_investments', 'status')) {
                $table->enum('status', ['active', 'completed', 'cancelled'])
                    ->default('active')
                    ->change();
            }
        });
    }

    public function down(): void
    {
        Schema::table('user_investments', function (Blueprint $table) {

            if (Schema::hasColumn('user_investments', 'daily_profit')) {
                $table->dropColumn('daily_profit');
            }

            if (Schema::hasColumn('user_investments', 'total_profit')) {
                $table->dropColumn('total_profit');
            }

            if (Schema::hasColumn('user_investments', 'last_paid_at')) {
                $table->dropColumn('last_paid_at');
            }

            // revert enum back to original if needed
            if (Schema::hasColumn('user_investments', 'status')) {
                $table->enum('status', ['ongoing', 'completed', 'cancelled'])
                    ->default('ongoing')
                    ->change();
            }
        });
    }
};
