<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tenant;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DefaultCategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->command->info('🏷️ Criando categorias padrão...');

        // Categorias padrão que todo usuário deve ter
        $defaultCategories = [
            // RECEITAS
            [
                'name' => 'Salário',
                'type' => 'income',
                'color' => '#10b981', // green-500
                'icon' => 'payments',
                'order' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Freelances',
                'type' => 'income',
                'color' => '#059669', // green-600
                'icon' => 'work',
                'order' => 2,
                'is_default' => true,
            ],
            [
                'name' => 'Investimentos',
                'type' => 'income',
                'color' => '#047857', // green-700
                'icon' => 'trending_up',
                'order' => 3,
                'is_default' => true,
            ],
            [
                'name' => 'Outros Rendimentos',
                'type' => 'income',
                'color' => '#065f46', // green-800
                'icon' => 'monetization_on',
                'order' => 4,
                'is_default' => true,
            ],

            // DESPESAS ESSENCIAIS
            [
                'name' => 'Alimentação',
                'type' => 'expense',
                'color' => '#ef4444', // red-500
                'icon' => 'restaurant',
                'order' => 1,
                'is_default' => true,
            ],
            [
                'name' => 'Transporte',
                'type' => 'expense',
                'color' => '#f97316', // orange-500
                'icon' => 'directions_car',
                'order' => 2,
                'is_default' => true,
            ],
            [
                'name' => 'Moradia',
                'type' => 'expense',
                'color' => '#eab308', // yellow-500
                'icon' => 'home',
                'order' => 3,
                'is_default' => true,
            ],
            [
                'name' => 'Saúde',
                'type' => 'expense',
                'color' => '#22c55e', // green-500
                'icon' => 'local_hospital',
                'order' => 4,
                'is_default' => true,
            ],
            [
                'name' => 'Educação',
                'type' => 'expense',
                'color' => '#3b82f6', // blue-500
                'icon' => 'school',
                'order' => 5,
                'is_default' => true,
            ],

            // DESPESAS PESSOAIS
            [
                'name' => 'Compras',
                'type' => 'expense',
                'color' => '#8b5cf6', // violet-500
                'icon' => 'shopping_bag',
                'order' => 6,
                'is_default' => true,
            ],
            [
                'name' => 'Entretenimento',
                'type' => 'expense',
                'color' => '#ec4899', // pink-500
                'icon' => 'movie',
                'order' => 7,
                'is_default' => true,
            ],
            [
                'name' => 'Vestuário',
                'type' => 'expense',
                'color' => '#f59e0b', // amber-500
                'icon' => 'checkroom',
                'order' => 8,
                'is_default' => true,
            ],

            // CONTAS E SERVIÇOS
            [
                'name' => 'Conta de Luz',
                'type' => 'expense',
                'color' => '#fbbf24', // yellow-400
                'icon' => 'flash_on',
                'order' => 9,
                'is_default' => true,
            ],
            [
                'name' => 'Conta de Água',
                'type' => 'expense',
                'color' => '#06b6d4', // cyan-500
                'icon' => 'water_drop',
                'order' => 10,
                'is_default' => true,
            ],
            [
                'name' => 'Internet/Telefone',
                'type' => 'expense',
                'color' => '#6366f1', // indigo-500
                'icon' => 'wifi',
                'order' => 11,
                'is_default' => true,
            ],
            [
                'name' => 'Streaming/Assinaturas',
                'type' => 'expense',
                'color' => '#8b5cf6', // violet-500
                'icon' => 'subscriptions',
                'order' => 12,
                'is_default' => true,
            ],

            // OUTROS
            [
                'name' => 'Poupança',
                'type' => 'expense', // Tecnicamente é uma saída de dinheiro
                'color' => '#059669', // green-600
                'icon' => 'savings',
                'order' => 13,
                'is_default' => true,
            ],
            [
                'name' => 'Emergências',
                'type' => 'expense',
                'color' => '#dc2626', // red-600
                'icon' => 'priority_high',
                'order' => 14,
                'is_default' => true,
            ],
            [
                'name' => 'Outros',
                'type' => 'expense',
                'color' => '#6b7280', // gray-500
                'icon' => 'more_horiz',
                'order' => 15,
                'is_default' => true,
            ],
        ];

        // Se não especificar tenant, criar para todos os usuários existentes
        if ($tenantId = $this->command->option('tenant')) {
            $tenants = Tenant::where('id', $tenantId)->get();
            $this->command->info("👤 Criando categorias para tenant ID: {$tenantId}");
        } else {
            $tenants = Tenant::all();
            $this->command->info("👥 Criando categorias para todos os usuários ({$tenants->count()})");
        }

        $totalCreated = 0;

        foreach ($tenants as $tenant) {
            $this->command->info("  📁 Processando usuário: {$tenant->name} ({$tenant->email})");
            
            foreach ($defaultCategories as $categoryData) {
                // Verificar se categoria já existe para este tenant
                $existingCategory = Category::where('tenant_id', $tenant->id)
                    ->where('name', $categoryData['name'])
                    ->where('type', $categoryData['type'])
                    ->first();

                if (!$existingCategory) {
                    Category::create(array_merge($categoryData, [
                        'tenant_id' => $tenant->id,
                        'is_active' => true,
                    ]));
                    $totalCreated++;
                } else {
                    $this->command->warn("    ⚠️ Categoria '{$categoryData['name']}' já existe - pulando");
                }
            }
        }

        $this->command->info("✅ {$totalCreated} categorias criadas com sucesso!");
    }
}
