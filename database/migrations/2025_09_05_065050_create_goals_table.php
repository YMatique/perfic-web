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
        Schema::create('goals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->enum('type', ['spending_limit', 'savings_target', 'category_limit', 'income_target']);
            $table->foreignId('category_id')->nullable()->constrained('categories')->onDelete('cascade');
            $table->string('name'); // "Meta Alimentação Março", "Poupança 2024"
            $table->decimal('target_amount', 10, 2);
            $table->enum('period', ['daily', 'weekly', 'monthly', 'quarterly', 'yearly']);
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->decimal('current_progress', 10, 2)->default(0);
            $table->timestamp('last_calculated_at')->nullable();
            $table->timestamps();

            $table->index(['tenant_id', 'is_active']);
            $table->index(['tenant_id', 'type', 'is_active']);
            $table->index(['start_date', 'end_date']);
        });
    }


    

        

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


    Schema::create('categorization_rules', function (Blueprint $table) {
        $table->id();
        $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
        $table->string('keyword');
        $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
        $table->decimal('confidence', 3, 2); // 0.00 to 1.00
        $table->enum('rule_type', ['keyword', 'regex', 'amount_range', 'location', 'merchant']);
        $table->json('rule_data')->nullable(); // additional rule parameters
        $table->integer('usage_count')->default(0);
        $table->integer('success_count')->default(0);
        $table->boolean('is_active')->default(true);
        $table->boolean('is_auto_generated')->default(true); // AI vs user created
        $table->timestamps();
        
        $table->index(['tenant_id', 'is_active']);
        $table->index(['keyword', 'tenant_id']);
        $table->index(['tenant_id', 'confidence']);
    });

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('goals');
    }
};
