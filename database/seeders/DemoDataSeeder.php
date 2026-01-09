<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Goal;
use App\Models\RecurringTransaction;
use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DemoDataSeeder extends Seeder
{
  /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //mudar tudo, de tenant para user

        $this->command->info('📊 Criando dados de demonstração...');

        // Definir tenant para demo
        if ($userId = $this->command->option('user')) {
            $user = User::find($userId);
            if (!$user) {
                $this->command->error("User com ID {$userId} não encontrado!");
                return;
            }
        } else {
            // Usar o primeiro usuário ou criar um usuário demo
            $user = User::first();
            if (!$user) {
                $this->command->warn('Nenhum usuário encontrado. Criando usuário demo...');
                $user = User::create([
                    'name' => 'Usuário Demo',
                    'email' => 'demo@perfic.com',
                    'password' => bcrypt('password'),
                    'email_verified_at' => now(),
                ]);
            }
        }

        $this->command->info("👤 Criando dados para: {$user->name}");

        // Garantir que o usuário tem categorias
        $categories = Category::where('user_id', $user->id)->get();
        if ($categories->isEmpty()) {
            $this->command->info('  📁 Usuário não tem categorias. Criando...');
            $this->call(DefaultCategoriesSeeder::class, false, ['--user' => $user->id]);
            $categories = Category::where('user_id', $user->id)->get();
        }

        // Separar categorias por tipo
        $incomeCategories = $categories->where('type', 'income');
        $expenseCategories = $categories->where('type', 'expense');

        $this->command->info('  💰 Criando transações de exemplo...');
        $this->createDemoTransactions($user, $incomeCategories, $expenseCategories);

        $this->command->info('  🔄 Criando transações recorrentes...');
        $this->createRecurringTransactions($user, $incomeCategories, $expenseCategories);

        $this->command->info('  🎯 Criando metas financeiras...');
        $this->createDemoGoals($user, $expenseCategories);

        $this->command->info('✅ Dados de demonstração criados com sucesso!');
    }

    private function createDemoTransactions($user, $incomeCategories, $expenseCategories)
    {
        $transactionsData = [];

        // Criar transações para os últimos 3 meses
        $startDate = now()->subMonths(3)->startOfMonth();
        $endDate = now()->endOfMonth();

        // RECEITAS (1-2 por mês)
        $salarioCategory = $incomeCategories->where('name', 'Salário')->first();
        $freelanceCategory = $incomeCategories->where('name', 'Freelances')->first();

        for ($month = 0; $month < 4; $month++) {
            $monthDate = now()->subMonths(3 - $month);
            
            // Salário mensal
            if ($salarioCategory) {
                $transactionsData[] = [
                    'user_id' => $user->id,
                    'category_id' => $salarioCategory->id,
                    'type' => 'income',
                    'amount' => rand(45000, 55000), // MZN 45,000 - 55,000
                    'description' => 'Salário - ' . $monthDate->format('F Y'),
                    'transaction_date' => $monthDate->day(5)->hour(9)->minute(0),
                ];
            }

            // Freelance ocasional
            if ($freelanceCategory && rand(1, 100) > 60) { // 40% chance
                $transactionsData[] = [
                    'user_id' => $user->id,
                    'category_id' => $freelanceCategory->id,
                    'type' => 'income',
                    'amount' => rand(8000, 25000),
                    'description' => 'Projeto freelance - Design de website',
                    'transaction_date' => $monthDate->day(rand(10, 25))->hour(rand(10, 18))->minute(0),
                ];
            }
        }

        // DESPESAS VARIADAS (3-5 por semana)
        $expenseTemplates = [
            // Alimentação
            ['category' => 'Alimentação', 'descriptions' => [
                'Compras no supermercado', 'Jantar no restaurante', 'Lanche na padaria', 
                'Delivery - Pizza', 'Feira da fruta', 'Café da manhã'
            ], 'amount_range' => [500, 3500]],
            
            // Transporte
            ['category' => 'Transporte', 'descriptions' => [
                'Combustível', 'Táxi/Uber', 'Manutenção do carro', 'Estacionamento'
            ], 'amount_range' => [300, 2000]],
            
            // Compras
            ['category' => 'Compras', 'descriptions' => [
                'Compras online', 'Farmácia', 'Produtos de limpeza', 'Eletrônicos'
            ], 'amount_range' => [800, 5000]],
            
            // Entretenimento
            ['category' => 'Entretenimento', 'descriptions' => [
                'Cinema', 'Streaming - Netflix', 'Show/Concerto', 'Bar com amigos', 'Jogos'
            ], 'amount_range' => [400, 2500]],
            
            // Contas
            ['category' => 'Conta de Luz', 'descriptions' => ['Conta de luz'], 'amount_range' => [1200, 1800]],
            ['category' => 'Conta de Água', 'descriptions' => ['Conta de água'], 'amount_range' => [800, 1200]],
            ['category' => 'Internet/Telefone', 'descriptions' => ['Conta de internet', 'Conta de telefone'], 'amount_range' => [1500, 2500]],
        ];

        // Gerar transações para últimos 90 dias
        for ($day = 90; $day >= 0; $day--) {
            $date = now()->subDays($day);
            
            // 1-3 transações por dia (mais nos fins de semana)
            $transactionsPerDay = $date->isWeekend() ? rand(1, 4) : rand(0, 2);
            
            for ($i = 0; $i < $transactionsPerDay; $i++) {
                $template = $expenseTemplates[array_rand($expenseTemplates)];
                $category = $expenseCategories->where('name', $template['category'])->first();
                
                if ($category) {
                    $transactionsData[] = [
                        'user_id' => $user->id,
                        'category_id' => $category->id,
                        'type' => 'expense',
                        'amount' => rand($template['amount_range'][0], $template['amount_range'][1]),
                        'description' => $template['descriptions'][array_rand($template['descriptions'])],
                        'transaction_date' => $date->hour(rand(8, 22))->minute(rand(0, 59)),
                    ];
                }
            }
        }

        // Inserir todas as transações
        foreach (array_chunk($transactionsData, 50) as $chunk) {
            foreach ($chunk as $transactionData) {
                Transaction::create($transactionData);
            }
        }

        $this->command->info("    ✅ " . count($transactionsData) . " transações criadas");
    }

    private function createRecurringTransactions($user, $incomeCategories, $expenseCategories)
    {
        $recurringData = [];

        // Salário mensal
        $salarioCategory = $incomeCategories->where('name', 'Salário')->first();
        if ($salarioCategory) {
            $recurringData[] = [
                'user_id' => $user->id,
                'category_id' => $salarioCategory->id,
                'type' => 'income',
                'amount' => 50000,
                'description' => 'Salário Mensal',
                'frequency' => 'monthly',
                'due_day' => 5,
                'start_date' => now()->startOfMonth(),
                'is_active' => true,
            ];
        }

        // Contas mensais
        $monthlyBills = [
            ['category' => 'Conta de Luz', 'amount' => 1500, 'day' => 10],
            ['category' => 'Conta de Água', 'amount' => 1000, 'day' => 15],
            ['category' => 'Internet/Telefone', 'amount' => 2000, 'day' => 20],
            ['category' => 'Streaming/Assinaturas', 'amount' => 500, 'day' => 25],
        ];

        foreach ($monthlyBills as $bill) {
            $category = $expenseCategories->where('name', $bill['category'])->first();
            if ($category) {
                $recurringData[] = [
                    'user_id' => $user->id,
                    'category_id' => $category->id,
                    'type' => 'expense',
                    'amount' => $bill['amount'],
                    'description' => $bill['category'] . ' - Automático',
                    'frequency' => 'monthly',
                    'due_day' => $bill['day'],
                    'start_date' => now()->startOfMonth(),
                    'is_active' => true,
                ];
            }
        }

        // Poupança mensal
        $poupancaCategory = $expenseCategories->where('name', 'Poupança')->first();
        if ($poupancaCategory) {
            $recurringData[] = [
                'user_id' => $user->id,
                'category_id' => $poupancaCategory->id,
                'type' => 'expense',
                'amount' => 5000,
                'description' => 'Poupança Automática',
                'frequency' => 'monthly',
                'due_day' => 6, // Logo após o salário
                'start_date' => now()->startOfMonth(),
                'is_active' => true,
            ];
        }

        // Criar transações recorrentes
        foreach ($recurringData as $data) {
            RecurringTransaction::create($data);
        }

        $this->command->info("    ✅ " . count($recurringData) . " transações recorrentes criadas");
    }

    private function createDemoGoals($user, $expenseCategories)
    {
        $goalsData = [];

        // Meta de gastos com alimentação
        $alimentacaoCategory = $expenseCategories->where('name', 'Alimentação')->first();
        if ($alimentacaoCategory) {
            $goalsData[] = [
                'user_id' => $user->id,
                'type' => 'category_limit',
                'category_id' => $alimentacaoCategory->id,
                'name' => 'Limite Alimentação - ' . now()->format('F Y'),
                'target_amount' => 15000, // MZN 15,000
                'period' => 'monthly',
                'start_date' => now()->startOfMonth(),
                'end_date' => now()->endOfMonth(),
                'is_active' => true,
            ];
        }

        // Meta de economia mensal
        $goalsData[] = [
            'user_id' => $user->id,
            'type' => 'savings_target',
            'name' => 'Meta de Economia - ' . now()->format('F Y'),
            'target_amount' => 8000, // MZN 8,000
            'period' => 'monthly',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_active' => true,
        ];

        // Meta anual de poupança
        $goalsData[] = [
            'user_id' => $user->id,
            'type' => 'savings_target',
            'name' => 'Reserva de Emergência 2024',
            'target_amount' => 100000, // MZN 100,000
            'period' => 'yearly',
            'start_date' => now()->startOfYear(),
            'end_date' => now()->endOfYear(),
            'is_active' => true,
        ];

        // Limite geral de gastos mensais
        $goalsData[] = [
            'user_id' => $user->id,
            'type' => 'spending_limit',
            'name' => 'Limite Total - ' . now()->format('F Y'),
            'target_amount' => 35000, // MZN 35,000
            'period' => 'monthly',
            'start_date' => now()->startOfMonth(),
            'end_date' => now()->endOfMonth(),
            'is_active' => true,
        ];

        // Criar metas
        foreach ($goalsData as $goalData) {
            $goal = Goal::create($goalData);
            
            // Calcular progresso inicial
            $goal->calculateProgress();
            $goal->save();
        }

        $this->command->info("    ✅ " . count($goalsData) . " metas criadas");
    }
}
