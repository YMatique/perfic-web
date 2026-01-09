<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PerficSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $this->command->info('🚀 Iniciando setup do Perfic...');
        $this->command->newLine();

        // Verificar se temos usuários
        $usersCount = User::count();
        $this->command->info("👥 Usuários existentes: {$usersCount}");

        if ($usersCount === 0) {
            $this->command->warn('⚠️ Nenhum usuário encontrado!');
            
            if ($this->command->confirm('Criar usuário demo para teste?', true)) {
                $this->createDemoUser();
            } else {
                $this->command->info('💡 Registre-se primeiro em /register, depois rode este seeder novamente.');
                return;
            }
        }

        // Opções do que fazer
        $this->command->newLine();
        $this->command->info('📋 Opções disponíveis:');
        $this->command->info('1. Categorias padrão (essencial para novos usuários)');
        $this->command->info('2. Dados de demonstração (transações, metas, recorrentes)');
        $this->command->info('3. Setup completo (categorias + dados demo)');
        
        $choice = $this->command->choice(
            'O que você quer fazer?',
            ['Apenas categorias', 'Apenas dados demo', 'Setup completo', 'Cancelar'],
            2 // Default: Setup completo
        );

        switch ($choice) {
            case 'Apenas categorias':
                $this->runCategories();
                break;
                
            case 'Apenas dados demo':
                $this->runDemoData();
                break;
                
            case 'Setup completo':
                $this->runCategories();
                $this->command->newLine();
                $this->runDemoData();
                break;
                
            case 'Cancelar':
                $this->command->info('❌ Operação cancelada.');
                return;
        }

        $this->command->newLine();
        $this->showSummary();
        $this->command->info('🎉 Setup do Perfic concluído com sucesso!');
    }
    private function createDemoUser()
    {
        $this->command->info('👤 Criando usuário demo...');
        
        $demoUser = User::create([
            'name' => 'Usuário Demo',
            'email' => 'demo@perfic.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);

        $this->command->info("✅ Usuário demo criado:");
        $this->command->info("   📧 Email: demo@perfic.com");
        $this->command->info("   🔑 Senha: password");
        $this->command->newLine();
    }

    private function runCategories()
    {
        $userOption = $this->getUserOption();
        
        $this->command->info('🏷️ Executando: Categorias Padrão');
        $this->call(DefaultCategoriesSeeder::class, false, $userOption ? ['--user' => $userOption] : []);
    }

    private function runDemoData()
    {
        $userOption = $this->getUserOption();
        
        $this->command->info('📊 Executando: Dados de Demonstração');
        $this->call(DemoDataSeeder::class, false, $userOption ? ['--user' => $userOption] : []);
    }

    private function getUserOption()
    {
        $users = User::all(['id', 'name', 'email']);
        
        if ($users->count() === 1) {
            $user = $users->first();
            $this->command->info("👤 Usando usuário: {$user->name} ({$user->email})");
            return $user->id;
        }

        if ($users->count() > 1) {
            $this->command->info('👥 Múltiplos usuários encontrados:');
            
            $choices = ['Todos os usuários'];
            foreach ($users as $user) {
                $choices[] = "{$user->name} ({$user->email})";
            }
            
            $choice = $this->command->choice('Para qual usuário?', $choices, 0);
            
            if ($choice === 'Todos os usuários') {
                return null; // Null = todos os usuários
            }
            
            // Encontrar o usuário selecionado
            $selectedIndex = array_search($choice, $choices) - 1; // -1 porque "Todos" é índice 0
            return $users->values()[$selectedIndex]->id;
        }

        return null;
    }

    private function showSummary()
    {
        $this->command->info('📊 Resumo Final:');
        
        // Estatísticas gerais
        $usersCount = User::count();
        $categoriesCount = \App\Models\Category::count();
        $transactionsCount = \App\Models\Transaction::count();
        $goalsCount = \App\Models\Goal::count();
        $recurringCount = \App\Models\RecurringTransaction::count();

        $this->command->table([
            'Item', 'Total'
        ], [
            ['👥 Usuários', $usersCount],
            ['🏷️ Categorias', $categoriesCount],
            ['💳 Transações', $transactionsCount],
            ['🎯 Metas', $goalsCount],
            ['🔄 Recorrentes', $recurringCount],
        ]);

        $this->command->newLine();
        $this->command->info('🎯 Próximos passos:');
        $this->command->info('1. Acesse /login (demo@perfic.com / password)');
        $this->command->info('2. Explore as funcionalidades com dados reais');
        $this->command->info('3. Configure o cron para transações recorrentes:');
        $this->command->info('   0 9 * * * php artisan perfic:execute-recurring');
    }
}
