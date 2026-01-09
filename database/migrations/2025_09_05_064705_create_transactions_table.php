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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            // $table->foreignId('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('category_id')->constrained('categories')->onDelete('cascade');
            $table->foreignId('recurring_transaction_id')->nullable()->constrained('recurring_transactions')->onDelete('set null');
            $table->enum('type', ['income', 'expense']);
            $table->decimal('amount', 10, 2);
            $table->text('description')->nullable();
            $table->timestamp('transaction_date');
            $table->boolean('is_recurring')->default(false);
            $table->boolean('categorized_by_ai')->default(false);
            $table->decimal('ai_confidence', 3, 2)->nullable(); // 0.00 to 1.00
            $table->string('location')->nullable();
            $table->json('attachments')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['user_id', 'transaction_date']);
            $table->index(['user_id', 'category_id', 'transaction_date']);
            $table->index(['user_id', 'type', 'transaction_date']);
            $table->index(['transaction_date']); // for analytics
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
