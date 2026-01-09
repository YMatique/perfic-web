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
        Schema::create('financial_scores', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('score', 5, 2); // 0.00 to 100.00
            $table->json('score_breakdown')->nullable(); // how score was calculated
            $table->date('calculated_for_month');
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->unique(['user_id', 'calculated_for_month']);
            $table->index(['user_id', 'calculated_for_month']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('financial_scores');
    }
};
