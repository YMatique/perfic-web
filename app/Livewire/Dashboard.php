<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Models\Transaction;
use App\Models\User;
use Livewire\Component;

class Dashboard extends Component
{
    // Dados do Dashboard
    public $summaryData = [];
    public $recentTransactions = [];
    public $monthlyTrends = [];
    public $topCategories = [];
    public $activeGoals = [];
    public $aiInsights = [];
    public $financialScore = null;
    public $title = 'Dashboard';
    public $pageTitle = 'Visão Geral';

    public function mount()
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData()
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return;
        }

        // 1. Resumo Financeiro (Mês Atual)
        $this->summaryData = $this->getSummaryData($user);

        // 2. Transações Recentes (Últimas 10)
        $this->recentTransactions = $this->getRecentTransactions($user);

        // 3. Tendências Mensais (Últimos 6 meses)
        $this->monthlyTrends = $this->getMonthlyTrends($user);

        // 4. Top Categorias (Mês Atual)
        $this->topCategories = $this->getTopCategories($user);

        // 5. Metas Ativas
        $this->activeGoals = $this->getActiveGoals($user);

        // 6. Insights de IA (3 mais recentes não lidos)
        $this->aiInsights = $this->getAiInsights($user);

        // 7. Score Financeiro
        $this->financialScore = $this->getFinancialScore($user);
    }

    private function getSummaryData($user)
    {
        $now = now();

        $currentMonthStart = $now->copy()->startOfMonth();
        $lastMonthStart = $now->copy()->subMonth()->startOfMonth();
        $lastMonthEnd = $now->copy()->subMonth()->endOfMonth();

        $currentMonthTransactions = Transaction::whereBetween(
            'transaction_date',
            [$currentMonthStart, $now]
        )->get();

        $currentIncome = $currentMonthTransactions->where('type', 'income')->sum('amount');
        $currentExpenses = $currentMonthTransactions->where('type', 'expense')->sum('amount');
        $currentBalance = $currentIncome - $currentExpenses;

        $lastMonthTransactions = Transaction::whereBetween(
            'transaction_date',
            [$lastMonthStart, $lastMonthEnd]
        )->get();

        $lastIncome = $lastMonthTransactions->where('type', 'income')->sum('amount');
        $lastExpenses = $lastMonthTransactions->where('type', 'expense')->sum('amount');

        // Calcular variações percentuais
        $incomeChange = $lastIncome > 0 ? (($currentIncome - $lastIncome) / $lastIncome * 100) : 0;
        $expenseChange = $lastExpenses > 0 ? (($currentExpenses - $lastExpenses) / $lastExpenses * 100) : 0;

        // Taxa de poupança
        $savingsRate = $currentIncome > 0 ? (($currentBalance / $currentIncome) * 100) : 0;

        // dd($currentIncome, $currentExpenses, $currentBalance, $incomeChange, $expenseChange, $savingsRate);
        return [
            'current_income' => $currentIncome,
            'current_expenses' => $currentExpenses,
            'current_balance' => $currentBalance,
            'income_change' => $incomeChange,
            'expense_change' => $expenseChange,
            'savings_rate' => $savingsRate,
            'transactions_count' => $currentMonthTransactions->count(),
        ];
    }

    private function getRecentTransactions($user)
    {
        return $user
            ->transactions()
            ->with('category')
            ->latest('transaction_date')
            ->take(10)
            ->get()
            ->map(function ($transaction) {
                return [
                    'id' => $transaction->id,
                    'description' => $transaction->description,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'date' => $transaction->transaction_date,
                    'category_name' => $transaction->category->name ?? 'Sem Categoria',
                    'category_icon' => $transaction->category->icon ?? 'help',
                    'category_color' => $transaction->category->color ?? '#6B7280',
                ];
            });
    }

    private function getMonthlyTrends($user)
    {
        $trends = [];

        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();

            $monthTransactions = $user
                ->transactions()
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->get();

            $trends[] = [
                'month' => $monthStart->format('M'),
                'year' => $monthStart->format('Y'),
                'income' => $monthTransactions->where('type', 'income')->sum('amount'),
                'expenses' => $monthTransactions->where('type', 'expense')->sum('amount'),
                'balance' => $monthTransactions->where('type', 'income')->sum('amount')
                    - $monthTransactions->where('type', 'expense')->sum('amount'),
            ];
        }

        return $trends;
    }

    private function getTopCategories($user)
    {
        $currentMonth = now()->startOfMonth();

        $categorySpending = $user
            ->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [$currentMonth, now()])
            ->with('category')
            ->get()
            ->groupBy('category_id')
            ->map(function ($transactions, $categoryId) use ($user) {
                $category = $user->categories()->find($categoryId);
                return [
                    'category_id' => $categoryId,
                    'name' => $category->name ?? 'Sem Categoria',
                    'icon' => $category->icon ?? 'help',
                    'color' => $category->color ?? '#6B7280',
                    'total' => $transactions->sum('amount'),
                    'count' => $transactions->count(),
                ];
            })
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $totalExpenses = $categorySpending->sum('total');

        return $categorySpending->map(function ($category) use ($totalExpenses) {
            $category['percentage'] = $totalExpenses > 0 ? ($category['total'] / $totalExpenses * 100) : 0;
            return $category;
        });
    }

    private function getActiveGoals($user)
    {
        return $user
            ->goals()
            ->where('is_active', true)
            ->get()
            ->each(function ($goal) {
                $goal->calculateProgress();
            })
            ->map(function ($goal) {
                return [
                    'id' => $goal->id,
                    'name' => $goal->name,
                    'type' => $goal->type,
                    'target_amount' => $goal->target_amount,
                    'current_progress' => $goal->current_progress,
                    'percentage' => $goal->progress_percentage,
                    'status' => $this->getGoalStatus($goal->progress_percentage),
                ];
            })
            ->take(4);
    }

    private function getAiInsights($user)
    {
        return $user
            ->aiInsights()
            ->where('is_read', false)
            ->latest()
            ->take(3)
            ->get()
            ->map(function ($insight) {
                return [
                    'id' => $insight->id,
                    'type' => $insight->type,
                    'title' => $insight->title,
                    'description' => $insight->description,
                    'impact_level' => $insight->impact_level,
                    'created_at' => $insight->created_at->diffForHumans(),
                ];
            });
    }

    private function getFinancialScore($user)
    {
        return $user
            ->financialScores()
            ->where('calculated_for_month', now()->format('Y-m-01'))
            ->latest('calculated_at')
            ->first();
    }

    private function getGoalStatus($percentage)
    {
        if ($percentage >= 100)
            return 'completed';
        if ($percentage >= 75)
            return 'on_track';
        if ($percentage >= 50)
            return 'progress';
        return 'needs_attention';
    }

    public function refreshData()
    {
        $this->loadDashboardData();
    }

    public function render()
    {
        return view('livewire.dashboard')->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle,
        ]);
    }
}
