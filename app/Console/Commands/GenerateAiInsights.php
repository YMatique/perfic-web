<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use App\Models\User;
use App\Services\AiAnalyzer;
use Illuminate\Console\Command;

class GenerateAiInsights extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perfic:generate-insights 
                            {--user= : Generate insights for specific user ID}
                            {--all : Generate for all users}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate AI insights for financial analysis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Gerando Insights de IA...');
        $this->newLine();

        $userId = $this->option('user');
        $all = $this->option('all');

        if ($userId) {
            $users = User::where('id', $userId)->get();
        } elseif ($all) {
            $users = User::all();
        } else {
            $this->warn('Especifique --user=ID ou --all');
            return self::FAILURE;
        }

        if ($users->isEmpty()) {
            $this->error('❌ Nenhum usuário encontrado!');
            return self::FAILURE;
        }

        $analyzer = new AiAnalyzer();
        $totalInsights = 0;

        $progressBar = $this->output->createProgressBar($users->count());
        $progressBar->start();

        foreach ($users as $user) {
            try {
                $insights = $analyzer->generateInsights($user);
                $totalInsights += $insights->count();
                
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Erro para {$user->name}: " . $e->getMessage());
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ {$totalInsights} insights gerados para {$users->count()} usuário(s)!");
        
        return self::SUCCESS;
    }
}
/**
 * COMO USAR:
 * 
 * # Gerar para todos os usuários
 * php artisan perfic:generate-insights --all
 * 
 * # Gerar para usuário específico
 * php artisan perfic:generate-insights --user=1
 * 
 * # Agendar para rodar diariamente (adicionar no routes/console.php):
 * Schedule::command('perfic:generate-insights --all')->dailyAt('06:00');
 */
