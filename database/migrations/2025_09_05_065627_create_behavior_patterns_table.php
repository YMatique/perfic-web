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
        Schema::create('behavior_patterns', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->enum('pattern_type', ['spending', 'income', 'category', 'temporal', 'location']);
            $table->string('pattern_key'); // "weekend_spending", "monthly_income", etc.
            $table->decimal('average_value', 10, 2);
            $table->integer('frequency');
            $table->decimal('confidence', 3, 2); // 0.00 to 1.00
            $table->json('pattern_data')->nullable(); // additional pattern info
            $table->timestamp('calculated_at');
            $table->timestamps();

            $table->index(['tenant_id', 'pattern_type']);
            $table->index(['tenant_id', 'confidence']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('behavior_patterns');
    }
};
