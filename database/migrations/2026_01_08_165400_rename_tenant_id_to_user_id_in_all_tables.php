<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Desabilitar foreign key checks temporariamente
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'categories',
            'transactions',
            'recurring_transactions',
            'goals',
            'financial_scores',
            'behavior_patterns',
            'ai_insights',
            'categorization_rules',
        ];
        
        foreach ($tables as $table) {
            // Drop foreign key constraint
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['tenant_id']);
            });
            
            // Rename column
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->renameColumn('tenant_id', 'user_id');
            });
            
            // Add new foreign key pointing to users table
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('user_id')
                    ->references('id')
                    ->on('users')
                    ->onDelete('cascade');
            });
        }
        
        // Reabilitar foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        
        $tables = [
            'categories',
            'transactions',
            'recurring_transactions',
            'goals',
            'financial_scores',
            'behavior_patterns',
            'ai_insights',
            'categorization_rules',
        ];
        
        foreach ($tables as $table) {
            // Drop foreign key
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->dropForeign(['user_id']);
            });
            
            // Rename column back
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->renameColumn('user_id', 'tenant_id');
            });
            
            // Restore original foreign key
            Schema::table($table, function (Blueprint $blueprint) {
                $blueprint->foreign('tenant_id')
                    ->references('id')
                    ->on('tenants')
                    ->onDelete('cascade');
            });
        }
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
