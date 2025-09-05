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
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorization_rules');
    }
};
