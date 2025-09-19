<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
         // Adicionar nova coluna description
        Schema::table('ai_insights', function (Blueprint $table) {
            $table->text('description')->nullable()->after('title');
            $table->enum('impact_level', ['low', 'medium', 'high'])->default('medium')->after('description');
            $table->json('data')->nullable()->after('impact_level');
            $table->foreignId('category_id')->nullable()->after('data')->constrained('categories')->onDelete('cascade');
        });

        // Migrar dados existentes: message -> description
        DB::statement('UPDATE ai_insights SET description = message WHERE description IS NULL');

        // Modificar o ENUM do type para incluir novos valores
        DB::statement("ALTER TABLE ai_insights MODIFY COLUMN type ENUM(
            'spending_pattern',
            'category_concentration',
            'anomaly',
            'trend',
            'savings_opportunity',
            'spending_alert',
            'savings_tip',
            'pattern_detected',
            'goal_progress',
            'budget_warning',
            'anomaly_detected'
        )");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
         Schema::table('ai_insights', function (Blueprint $table) {
            $table->dropColumn(['description', 'impact_level', 'data', 'category_id']);
        });
        
        // Reverter ENUM
        DB::statement("ALTER TABLE ai_insights MODIFY COLUMN type ENUM(
            'spending_alert',
            'savings_tip',
            'pattern_detected',
            'goal_progress',
            'budget_warning',
            'anomaly_detected'
        )");
    }
};
