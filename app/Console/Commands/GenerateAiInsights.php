<?php

namespace App\Console\Commands;

use App\Models\Tenant;
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
                            {--tenant= : Generate insights for specific tenant ID}
                            {--all : Generate for all tenants}';

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

        $tenantId = $this->option('tenant');
        $all = $this->option('all');

        if ($tenantId) {
            $tenants = Tenant::where('id', $tenantId)->get();
        } elseif ($all) {
            $tenants = Tenant::all();
        } else {
            $this->warn('Especifique --tenant=ID ou --all');
            return self::FAILURE;
        }

        if ($tenants->isEmpty()) {
            $this->error('❌ Nenhum tenant encontrado!');
            return self::FAILURE;
        }

        $analyzer = new AiAnalyzer();
        $totalInsights = 0;

        $progressBar = $this->output->createProgressBar($tenants->count());
        $progressBar->start();

        foreach ($tenants as $tenant) {
            try {
                $insights = $analyzer->generateInsights($tenant);
                $totalInsights += $insights->count();
                
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("❌ Erro para {$tenant->name}: " . $e->getMessage());
                $progressBar->advance();
            }
        }

        $progressBar->finish();
        $this->newLine(2);

        $this->info("✅ {$totalInsights} insights gerados para {$tenants->count()} usuário(s)!");
        
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
 * php artisan perfic:generate-insights --tenant=1
 * 
 * # Agendar para rodar diariamente (adicionar no routes/console.php):
 * Schedule::command('perfic:generate-insights --all')->dailyAt('06:00');
 */
