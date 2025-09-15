<?php

namespace Database\Seeders;

use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class TenantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Pega o primeiro usuário ou cria um se não existir
        $user = User::first();

        if (!$user) {
            $user = User::create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'password' => bcrypt('password'),
                'email_verified_at' => now(),
            ]);
        }

        // Verifica se já existe um tenant com esse ID
        $existingTenant = Tenant::find($user->id);

        if (!$existingTenant) {
            // Cria tenant com o mesmo ID do usuário para resolver o foreign key
            Tenant::create([
                'id' => $user->id,
                'uuid' => Str::uuid(),
                'name' => $user->name,
                'email' => $user->email,
                'password' => $user->password,
                'email_verified_at' => $user->email_verified_at,
                'settings' => [
                    'currency' => 'MZN',
                    'timezone' => 'Africa/Maputo',
                    'language' => 'pt_MZ'
                ]
            ]);

            $this->command->info("Tenant criado com ID: {$user->id}");
        } else {
            $this->command->info("Tenant já existe com ID: {$user->id}");
        }

        // Criar algumas categorias padrão para testar
        $this->createDefaultCategories($user->id);
    }
    private function createDefaultCategories($tenantId)
    {
        $defaultCategories = [
            // Despesas
            [
                'name' => 'Alimentação',
                'type' => 'expense',
                'color' => '#ef4444',
                'icon' => 'restaurant',
                'order' => 1
            ],
            [
                'name' => 'Transporte',
                'type' => 'expense',
                'color' => '#3b82f6',
                'icon' => 'directions_car',
                'order' => 2
            ],
            [
                'name' => 'Casa',
                'type' => 'expense',
                'color' => '#8b5cf6',
                'icon' => 'home',
                'order' => 3
            ],
            [
                'name' => 'Saúde',
                'type' => 'expense',
                'color' => '#10b981',
                'icon' => 'medical_services',
                'order' => 4
            ],

            // Receitas
            [
                'name' => 'Salário',
                'type' => 'income',
                'color' => '#22c55e',
                'icon' => 'work',
                'order' => 5
            ],
            [
                'name' => 'Freelance',
                'type' => 'income',
                'color' => '#f59e0b',
                'icon' => 'laptop',
                'order' => 6
            ]
        ];

        foreach ($defaultCategories as $category) {
            \App\Models\Category::firstOrCreate(
                [
                    'tenant_id' => $tenantId,
                    'name' => $category['name'],
                    'type' => $category['type']
                ],
                [
                    'color' => $category['color'],
                    'icon' => $category['icon'],
                    'is_active' => true,
                    'order' => $category['order'],
                    'is_default' => true
                ]
            );
        }

        $this->command->info("Categorias padrão criadas para tenant ID: {$tenantId}");
    }
}
