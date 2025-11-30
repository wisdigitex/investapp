<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('user_investments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('package_id')->constrained('investment_packages')->cascadeOnDelete();
            $table->decimal('amount', 10, 2);
            $table->decimal('expected_payout', 10, 2);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('status', ['ongoing', 'completed', 'cancelled'])->default('ongoing');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_investments');
    }
};
