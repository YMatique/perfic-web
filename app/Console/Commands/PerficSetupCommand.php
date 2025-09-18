<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class PerficSetupCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'perfic:setup 
                            {--categories : Install only default categories}
                            {--demo : Install only demo data}
                            {--tenant= : Setup only for specific tenant ID}
                            {--force : Skip confirmations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Setup Perfic with default categories and demo data';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->showHeader();

        $categoriesOnly = $this->option('categories');
        $demoOnly = $this->option('demo');
        $tenantId = $this->option('tenant');
        $force = $this->option('force');

        // Determine what to run
        if ($categoriesOnly) {
            $this->runCategories($tenantId);
        } elseif ($demoOnly) {
            $this->runDemoData($tenantId);
        } else {
            // Full setup
            $this->runFullSetup($tenantId, $force);
        }

        $this->showFooter();

        return self::SUCCESS;
    }

    private function showHeader()
    {
        $this->info('');
        $this->info('  ╔═══════════════════════════════════╗');
        $this->info('  ║            PERFIC SETUP           ║');
        $this->info('  ║   Sistema de Controle Financeiro  ║');
        $this->info('  ╚═══════════════════════════════════╝');
        $this->info('');
    }

    private function runCategories($tenantId = null)
    {
        $this->info('🏷️ Instalando categorias padrão...');

        // $options = $tenantId ? ['--tenant' => $tenantId] : [];
        // $this->call('db:seed', [
        //     '--class' => 'DefaultCategoriesSeeder',
        // ] + $options);
        // Verificar se temos usuários
        if (! $tenantId) {
            $usersCount = Tenant::count();
            if ($usersCount === 0) {
                $this->warn('⚠️ Nenhum usuário encontrado!');
                if ($this->confirm('Criar usuário demo para teste?', true)) {
                    $this->createDemoUser();
                }
            }
        }

        // Chamar o seeder corretamente - usando a classe diretamente
        $seeder = new \Database\Seeders\DefaultCategoriesSeeder;
        $seeder->setCommand($this);
        $seeder->setContainer(app());

        if ($tenantId) {
            // Se especificou tenant, passar via environment/config temporário
            config(['seeder.tenant_id' => $tenantId]);
        }

        $seeder->run();
    }

    private function runDemoData($tenantId = null)
    {
        $this->info('📊 Instalando dados de demonstração...');

        // $options = $tenantId ? ['--tenant' => $tenantId] : [];
        // $this->call('db:seed', [
        //     '--class' => 'DemoDataSeeder',
        // ] + $options);
        $seeder = new \Database\Seeders\DemoDataSeeder;
        $seeder->setCommand($this);
        $seeder->setContainer(app());

        if ($tenantId) {
            config(['seeder.tenant_id' => $tenantId]);
        }

        $seeder->run();
    }

    private function runFullSetup($tenantId = null, $force = false)
    {
        if (! $force) {
            $this->warn('⚠️ Isso vai criar categorias padrão e dados de exemplo.');
            if (! $this->confirm('Continuar?', true)) {
                $this->info('❌ Setup cancelado.');

                return;
            }
        }

        // Run categories first
        $this->runCategories($tenantId);
        $this->newLine();

        // Then demo data
        $this->runDemoData($tenantId);
    }

     private function createDemoUser()
    {
        $this->info('👤 Criando usuário demo...');
        
        $demoUser = Tenant::create([
            'name' => 'Usuário Demo',
            'email' => 'demo@perfic.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $this->info("✅ Usuário demo criado:");
        $this->info("   📧 Email: demo@perfic.com");
        $this->info("   🔑 Senha: password");
        $this->newLine();
        
        return $demoUser;
    }

    private function showFooter()
    {
        $this->newLine();
        $this->info('✅ Setup concluído!');
        $this->newLine();

        $this->info('🎯 Próximos passos:');
        $this->info('• Acesse: http://localhost:8000/login');
        $this->info('• Demo: demo@perfic.com / password');
        $this->info('• Configure cron: 0 9 * * * php artisan perfic:execute-recurring');
        $this->newLine();
    }
}
