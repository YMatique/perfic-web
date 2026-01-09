<?php

namespace App\Services;

use App\Models\AiInsight;
use App\Models\FinancialScore;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Support\Collection;

class AiAnalyzer
{
      /**
     * Gerar todos os insights para um tenant
     */
    public function generateInsights(User $user): Collection
    {
        $insights = collect();

        // 1. Análise de padrões de gasto
        $insights = $insights->merge($this->analyzeSpendingPatterns($user));

        // 2. Detecção de anomalias
        $insights = $insights->merge($this->detectAnomalies($user));

        // 3. Análise de tendências
        $insights = $insights->merge($this->analyzeTrends($user));

        // 4. Recomendações de economia
        $insights = $insights->merge($this->generateSavingsRecommendations($user));

        // 5. Score financeiro
        $this->calculateFinancialScore($user);

        // Salvar insights no banco
        foreach ($insights as $insight) {
            AiInsight::create([
                'user_id' => $user->id,
                'type' => $insight['type'],
                'title' => $insight['title'],
                'message'=>'',
                'description' => $insight['description'],
                'impact_level' => $insight['impact_level'],
                'category_id' => $insight['category_id'] ?? null,
                'data' => $insight['data'] ?? null, // ✅ Laravel converte array para JSON automaticamente
                'is_read' => false,
            ]);
        }

        return $insights;
    }

    /**
     * Analisar padrões de gastos temporais
     */
    private function analyzeSpendingPatterns(User  $user): array
    {
        $insights = [];
        
        // Últimos 3 meses de transações
        $transactions = $user->transactions()
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->get();

        if ($transactions->count() < 10) {
            return $insights;
        }

        // Análise por dia da semana
        $weekdaySpending = $transactions->groupBy(function ($t) {
            return $t->transaction_date->dayOfWeek;
        })->map->sum('amount');

        $avgDaily = $weekdaySpending->avg();
        
        foreach ($weekdaySpending as $day => $amount) {
            $percentDiff = (($amount - $avgDaily) / $avgDaily) * 100;
            
            if ($percentDiff > 50) {
                $dayName = ['Domingo', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado'][$day];
                
                $insights[] = [
                    'type' => 'spending_pattern',
                    'title' => "Pico de gastos às {$dayName}s",
                    'description' => sprintf(
                        "Você gasta %.0f%% mais às %ss (MZN %s) comparado à média semanal (MZN %s)",
                        $percentDiff,
                        $dayName,
                        number_format($amount, 2, ',', '.'),
                        number_format($avgDaily, 2, ',', '.')
                    ),
                    'impact_level' => $percentDiff > 100 ? 'high' : 'medium',
                    'data' => [
                        'day' => $day,
                        'amount' => $amount,
                        'average' => $avgDaily,
                        'percentage' => $percentDiff
                    ]
                ];
            }
        }

        // Análise por categoria
        $categorySpending = $transactions->groupBy('category_id')
            ->map(function ($items) {
                return [
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                    'avg' => $items->avg('amount')
                ];
            });

        $totalSpending = $transactions->sum('amount');
        
        foreach ($categorySpending as $categoryId => $data) {
            $percentage = ($data['total'] / $totalSpending) * 100;
            
            if ($percentage > 30) {
                $category = $user->categories()->find($categoryId);
                
                $insights[] = [
                    'type' => 'category_concentration',
                    'title' => "Alto gasto em {$category->name}",
                    'description' => sprintf(
                        "%.0f%% dos seus gastos (MZN %s) estão em %s. Considere revisar esta categoria.",
                        $percentage,
                        number_format($data['total'], 2, ',', '.'),
                        $category->name
                    ),
                    'impact_level' => $percentage > 50 ? 'high' : 'medium',
                    'category_id' => $categoryId,
                    'data' => [
                        'percentage' => $percentage,
                        'total' => $data['total']
                    ]
                ];
            }
        }

        return $insights;
    }

    /**
     * Detectar anomalias nos gastos
     */
    private function detectAnomalies(User $user): array
    {
        $insights = [];
        
        $recentTransactions = $user->transactions()
            ->where('type', 'expense')
            ->where('transaction_date', '>=', now()->subMonth())
            ->get();

        $historicalTransactions = $user->transactions()
            ->where('type', 'expense')
            ->where('transaction_date', '<', now()->subMonth())
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->get();

        if ($historicalTransactions->count() < 10) {
            return $insights;
        }

        // Calcular estatísticas por categoria
        $categoryStats = $historicalTransactions->groupBy('category_id')
            ->map(function ($items) {
                $amounts = $items->pluck('amount');
                return [
                    'mean' => $amounts->avg(),
                    'stddev' => $this->calculateStdDev($amounts),
                    'max' => $amounts->max()
                ];
            });

        // Detectar transações anômalas
        foreach ($recentTransactions as $transaction) {
            if (!isset($categoryStats[$transaction->category_id])) {
                continue;
            }

            $stats = $categoryStats[$transaction->category_id];
            $zScore = ($transaction->amount - $stats['mean']) / ($stats['stddev'] ?: 1);

            // Z-score > 2.5 = anomalia (muito acima do normal)
            if ($zScore > 2.5) {
                $category = $user->categories()->find($transaction->category_id);
                $percentAbove = (($transaction->amount - $stats['mean']) / $stats['mean']) * 100;

                $insights[] = [
                    'type' => 'anomaly',
                    'title' => "Gasto atípico em {$category->name}",
                    'description' => sprintf(
                        "Transação de MZN %s em %s está %.0f%% acima da sua média (MZN %s). Descrição: %s",
                        number_format($transaction->amount, 2, ',', '.'),
                        $category->name,
                        $percentAbove,
                        number_format($stats['mean'], 2, ',', '.'),
                        $transaction->description
                    ),
                    'impact_level' => $zScore > 4 ? 'high' : 'medium',
                    'category_id' => $transaction->category_id,
                    'data' => [
                        'transaction_id' => $transaction->id,
                        'amount' => $transaction->amount,
                        'mean' => $stats['mean'],
                        'z_score' => $zScore
                    ]
                ];
            }
        }

        return $insights;
    }

    /**
     * Analisar tendências de crescimento/redução
     */
    private function analyzeTrends(User $user): array
    {
        $insights = [];

        // Comparar últimos 3 meses vs 3 meses anteriores
        $recentPeriod = $user->transactions()
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->where('type', 'expense')
            ->get();

        $previousPeriod = $user->transactions()
            ->where('transaction_date', '<', now()->subMonths(3))
            ->where('transaction_date', '>=', now()->subMonths(6))
            ->where('type', 'expense')
            ->get();

        if ($previousPeriod->count() < 5) {
            return $insights;
        }

        // Por categoria
        $recentByCategory = $recentPeriod->groupBy('category_id')->map->sum('amount');
        $previousByCategory = $previousPeriod->groupBy('category_id')->map->sum('amount');

        foreach ($recentByCategory as $categoryId => $recentAmount) {
            $previousAmount = $previousByCategory[$categoryId] ?? 0;
            
            if ($previousAmount == 0) continue;

            $percentChange = (($recentAmount - $previousAmount) / $previousAmount) * 100;

            if (abs($percentChange) > 30) {
                $category = $user->categories()->find($categoryId);
                $trend = $percentChange > 0 ? 'aumentaram' : 'diminuíram';
                
                $insights[] = [
                    'type' => 'trend',
                    'title' => "Tendência em {$category->name}",
                    'description' => sprintf(
                        "Seus gastos em %s %s %.0f%% nos últimos 3 meses (de MZN %s para MZN %s)",
                        $category->name,
                        $trend,
                        abs($percentChange),
                        number_format($previousAmount, 2, ',', '.'),
                        number_format($recentAmount, 2, ',', '.')
                    ),
                    'impact_level' => abs($percentChange) > 50 ? 'high' : 'medium',
                    'category_id' => $categoryId,
                    'data' => [
                        'recent_amount' => $recentAmount,
                        'previous_amount' => $previousAmount,
                        'percent_change' => $percentChange
                    ]
                ];
            }
        }

        return $insights;
    }

    /**
     * Gerar recomendações de economia
     */
    private function generateSavingsRecommendations(User $user): array
    {
        $insights = [];

        $transactions = $user->transactions()
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->where('type', 'expense')
            ->get();

        if ($transactions->count() < 10) {
            return $insights;
        }

        $categorySpending = $transactions->groupBy('category_id')
            ->map->sum('amount')
            ->sortDesc();

        $totalSpending = $transactions->sum('amount');

        // Top 3 categorias com maior gasto
        foreach ($categorySpending->take(3) as $categoryId => $amount) {
            $category = $user->categories()->find($categoryId);
            $percentage = ($amount / $totalSpending) * 100;

            // Sugerir redução de 20%
            $potentialSavings = $amount * 0.20;
            $monthlySavings = $potentialSavings / 3;

            $insights[] = [
                'type' => 'savings_opportunity',
                'title' => "Oportunidade de economia em {$category->name}",
                'description' => sprintf(
                    "Reduzindo 20%% dos gastos em %s, você economizaria MZN %s por mês (%.0f%% do total de gastos)",
                    $category->name,
                    number_format($monthlySavings, 2, ',', '.'),
                    $percentage * 0.20
                ),
                'impact_level' => $monthlySavings > 2000 ? 'high' : 'medium',
                'category_id' => $categoryId,
                'data' => [
                    'current_amount' => $amount,
                    'potential_savings' => $potentialSavings,
                    'monthly_savings' => $monthlySavings
                ]
            ];
        }

        return $insights;
    }

    /**
     * Calcular score financeiro (0-100)
     */
    private function calculateFinancialScore(User $user): void
    {
        $currentMonth = now()->format('Y-m-01');
        
        $monthTransactions = $user->transactions()
            ->where('transaction_date', '>=', $currentMonth)
            ->get();

        $income = $monthTransactions->where('type', 'income')->sum('amount');
        $expenses = $monthTransactions->where('type', 'expense')->sum('amount');

        // Componentes do score
        $savingsRate = $income > 0 ? (($income - $expenses) / $income) * 100 : 0;
        $savingsScore = min($savingsRate * 3, 30); // Max 30 pontos

        // Aderência às metas
        $goals = $user->goals()->where('is_active', true)->get();
        $goalsScore = 0;
        if ($goals->count() > 0) {
            $goalsMetPercentage = $goals->filter(function ($goal) {
                return $goal->progress_percentage >= 100;
            })->count() / $goals->count();
            $goalsScore = $goalsMetPercentage * 25; // Max 25 pontos
        }

        // Consistência (baixo desvio padrão = consistente)
        $last3Months = $user->transactions()
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->where('type', 'expense')
            ->get()
            ->groupBy(function ($t) {
                return $t->transaction_date->format('Y-m');
            })
            ->map->sum('amount');

        $consistencyScore = 0;
        if ($last3Months->count() >= 2) {
            $stdDev = $this->calculateStdDev($last3Months);
            $mean = $last3Months->avg();
            $coefficientOfVariation = $mean > 0 ? ($stdDev / $mean) : 1;
            $consistencyScore = max(0, 20 * (1 - $coefficientOfVariation)); // Max 20 pontos
        }

        // Diversificação (não gastar tudo em uma categoria)
        $categoryCount = $monthTransactions->where('type', 'expense')
            ->pluck('category_id')
            ->unique()
            ->count();
        $diversificationScore = min($categoryCount * 3, 15); // Max 15 pontos

        // Reserva de emergência (simples: saldo atual)
        $currentBalance = $income - $expenses;
        $emergencyScore = $currentBalance > $expenses * 3 ? 10 : ($currentBalance > 0 ? 5 : 0);

        // Score final
        $finalScore = round($savingsScore + $goalsScore + $consistencyScore + $diversificationScore + $emergencyScore);

        // Salvar no banco
        FinancialScore::updateOrCreate(
            [
                'user_id' => $user->id,
                'calculated_for_month' => $currentMonth,
            ],
            [
                'score' => $finalScore,
                'savings_rate' => $savingsRate,
                'budget_adherence' => $goalsScore,
                'consistency_score' => $consistencyScore,
                'calculated_at' => now(),
                'insights' => [
                    'savings_score' => $savingsScore,
                    'goals_score' => $goalsScore,
                    'consistency_score' => $consistencyScore,
                    'diversification_score' => $diversificationScore,
                    'emergency_score' => $emergencyScore,
                ]
            ]
        );
    }

    /**
     * Calcular desvio padrão
     */
    private function calculateStdDev(Collection $values): float
    {
        if ($values->count() < 2) {
            return 0;
        }

        $mean = $values->avg();
        $variance = $values->map(function ($value) use ($mean) {
            return pow($value - $mean, 2);
        })->avg();

        return sqrt($variance);
    }

    /**
     * Detectar perfil do usuário
     */
    public function detectUserProfile(User $user): string
    {
        $transactions = $user->transactions()
            ->where('transaction_date', '>=', now()->subMonths(3))
            ->get();

        $income = $transactions->where('type', 'income')->sum('amount');
        $expenses = $transactions->where('type', 'expense')->sum('amount');
        $savingsRate = $income > 0 ? (($income - $expenses) / $income) * 100 : 0;

        $weekendSpending = $transactions->where('type', 'expense')
            ->filter(fn($t) => in_array($t->transaction_date->dayOfWeek, [0, 6]))
            ->sum('amount');

        $weekdaySpending = $transactions->where('type', 'expense')
            ->filter(fn($t) => !in_array($t->transaction_date->dayOfWeek, [0, 6]))
            ->sum('amount');

        $avgWeekend = $weekendSpending / 8; // 8 dias de fim de semana em 4 semanas
        $avgWeekday = $weekdaySpending / 20; // 20 dias úteis em 4 semanas

        // Classificação
        if ($savingsRate > 25) {
            return 'Poupador Consistente';
        } elseif ($avgWeekend > $avgWeekday * 1.5) {
            return 'Gastador de Fim de Semana';
        } elseif ($savingsRate < 5 && $expenses > $income * 0.95) {
            return 'Gastador Impulsivo';
        } elseif ($savingsRate >= 10 && $savingsRate <= 25) {
            return 'Equilibrado';
        } else {
            return 'Em Construção';
        }
    }
}
