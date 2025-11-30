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
        Schema::create('profile_levels', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Basic, Pro, VIP
            $table->decimal('upgrade_price', 10, 2)->default(0);
            $table->decimal('min_withdrawal', 10, 2)->default(50);
            $table->integer('max_daily_withdrawals')->default(1);
            $table->boolean('requires_referrals')->default(false);
            $table->integer('min_referrals')->default(0);
            $table->json('unlocks')->nullable(); // e.g. extra limits
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profile_levels');
    }
};
