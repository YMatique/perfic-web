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
        Schema::create('ai_insights', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->enum('type', ['spending_alert', 'savings_tip', 'pattern_detected', 'goal_progress', 'budget_warning', 'anomaly_detected']);
            $table->string('title');
            $table->text('message');
            $table->foreignId('related_category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->foreignId('related_goal_id')->nullable()->constrained('goals')->onDelete('cascade');
            $table->decimal('impact_value', 10, 2)->nullable();
            $table->enum('priority', ['low', 'medium', 'high', 'urgent']);
            $table->boolean('is_read')->default(false);
            $table->boolean('is_actionable')->default(false);
            $table->json('action_data')->nullable(); // suggested actions
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_read', 'priority']);
            $table->index(['tenant_id', 'created_at']);
            $table->index(['expires_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ai_insights');
    }
};
