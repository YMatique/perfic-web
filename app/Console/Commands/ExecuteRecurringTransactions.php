<?php

namespace App\Console\Commands;

use App\Models\RecurringTransaction;
use Illuminate\Console\Command;

class ExecuteRecurringTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perfic:execute-recurring 
                            {--dry-run : Show what would be executed without actually executing}
                            {--user= : Execute only for specific user ID}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Execute all due recurring transactions';

     /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $userId = $this->option('user');

        $this->info('🔄 Checking for due recurring transactions...');
        $this->newLine();

        // Build query
        $query = RecurringTransaction::dueForExecution();
        
        if ($userId) {
            $query->where('user_id', $userId);
            $this->info("📍 Filtering for user ID: {$userId}");
        }

        $dueTransactions = $query->with(['user', 'category'])->get();

        if ($dueTransactions->isEmpty()) {
            $this->info('✅ No recurring transactions are due for execution.');
            return self::SUCCESS;
        }

        $this->info("📋 Found {$dueTransactions->count()} due transaction(s):");
        $this->newLine();

        // Display table of due transactions
        $tableData = [];
        foreach ($dueTransactions as $recurring) {
            $tableData[] = [
                'ID' => $recurring->id,
                'User' => $recurring->user->name ?? 'N/A',
                'Description' => $recurring->description,
                'Amount' => $recurring->formatted_amount,
                'Type' => ucfirst($recurring->type),
                'Category' => $recurring->category->name ?? 'N/A',
                'Due Date' => $recurring->next_execution?->format('Y-m-d H:i') ?? 'N/A',
                'Frequency' => ucfirst($recurring->frequency),
            ];
        }

        $this->table([
            'ID', 'User', 'Description', 'Amount', 'Type', 'Category', 'Due Date', 'Frequency'
        ], $tableData);

        if ($isDryRun) {
            $this->warn('🔍 DRY RUN: No transactions were actually executed.');
            return self::SUCCESS;
        }

        // Ask for confirmation in production
        if (app()->environment('production')) {
            if (!$this->confirm('Do you want to proceed with executing these transactions?')) {
                $this->info('❌ Operation cancelled.');
                return self::SUCCESS;
            }
        }

        $this->newLine();
        $this->info('🚀 Executing transactions...');

        // Execute transactions
        $executed = 0;
        $errors = 0;
        $progressBar = $this->output->createProgressBar($dueTransactions->count());
        $progressBar->finish();
        $this->newLine(2);

        // Show results
        if ($executed > 0) {
            $this->info("✅ Successfully executed {$executed} transaction(s).");
        }

        if ($errors > 0) {
            $this->error("❌ Failed to execute {$errors} transaction(s). Check logs for details.");
        }

        // Show summary
        $this->newLine();
        $this->info('📊 Execution Summary:');
        $this->table([
            'Status', 'Count'
        ], [
            ['✅ Executed', $executed],
            ['❌ Failed', $errors],
            ['📋 Total', $dueTransactions->count()],
        ]);

        return $errors === 0 ? self::SUCCESS : self::FAILURE;
    }
}
/***
 * 
 * # Ver o que seria executado (teste)
php artisan perfic:execute-recurring --dry-run

# Executar todas as transações pendentes  
php artisan perfic:execute-recurring

# Executar apenas para um usuário específico
php artisan perfic:execute-recurring --user=123

# Combinado - teste para usuário específico
php artisan perfic:execute-recurring --dry-run --user=123
 */
