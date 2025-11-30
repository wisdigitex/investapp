<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {

            if (!Schema::hasColumn('transactions', 'payment_method')) {
                $table->string('payment_method')->nullable();
            }

            if (!Schema::hasColumn('transactions', 'wallet_address')) {
                $table->string('wallet_address')->nullable();
            }

            if (!Schema::hasColumn('transactions', 'tx_hash')) {
                $table->string('tx_hash')->nullable();
            }

            if (!Schema::hasColumn('transactions', 'screenshot')) {
                $table->string('screenshot')->nullable();
            }

            if (!Schema::hasColumn('transactions', 'notes')) {
                $table->text('notes')->nullable();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropColumn([
                'payment_method',
                'wallet_address',
                'tx_hash',
                'screenshot',
                'notes'
            ]);
        });
    }
};
