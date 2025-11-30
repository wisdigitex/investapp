<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('telegram_id')->nullable()->unique();
            $table->string('referral_code')->nullable()->unique();
            $table->foreignId('referred_by')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('main_balance', 12, 2)->default(0);
            $table->decimal('earnings_balance', 12, 2)->default(0);
            $table->foreignId('profile_level_id')->default(1)->constrained('profile_levels');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'telegram_id', 'referral_code', 'referred_by',
                'main_balance','earnings_balance','profile_level_id'
            ]);
        });
    }
};
