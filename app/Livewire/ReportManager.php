<?php

namespace App\Livewire;

use App\Models\Tenant;
use App\Traits\WithToast;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ReportManager extends Component
{
     use WithToast;

    // Filters
    public $reportType = 'summary'; // summary, category, goals, comparison
    public $period = 'current_month';
    public $startDate;
    public $endDate;
    public $categoryId = null;
    public $customPeriod = false;

    // Data
    public $reportData = [];
    public $loading = false;

    // UI
    public $title = 'Relatórios Financeiros';
    public $pageTitle = 'Relatórios';

    protected $rules = [
        'startDate' => 'required_if:customPeriod,true|date',
        'endDate' => 'required_if:customPeriod,true|date|after_or_equal:startDate',
    ];

    public function mount()
    {
        $this->initializeDates();
        $this->generateReport();
    }

    public function initializeDates()
    {
        switch ($this->period) {
            case 'current_month':
                $this->startDate = now()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_month':
                $this->startDate = now()->subMonth()->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->subMonth()->endOfMonth()->format('Y-m-d');
                break;
            case 'current_year':
                $this->startDate = now()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->endOfYear()->format('Y-m-d');
                break;
            case 'last_year':
                $this->startDate = now()->subYear()->startOfYear()->format('Y-m-d');
                $this->endDate = now()->subYear()->endOfYear()->format('Y-m-d');
                break;
            case 'last_3_months':
                $this->startDate = now()->subMonths(3)->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
            case 'last_6_months':
                $this->startDate = now()->subMonths(6)->startOfMonth()->format('Y-m-d');
                $this->endDate = now()->endOfMonth()->format('Y-m-d');
                break;
        }
    }

    public function updatedPeriod()
    {
        $this->customPeriod = $this->period === 'custom';
        if (!$this->customPeriod) {
            $this->initializeDates();
            $this->generateReport();
        }
    }

    public function updatedStartDate()
    {
        if ($this->customPeriod) {
            $this->generateReport();
        }
    }

    public function updatedEndDate()
    {
        if ($this->customPeriod) {
            $this->generateReport();
        }
    }

    public function updatedReportType()
    {
        $this->generateReport();
    }

    public function updatedCategoryId()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $this->loading = true;

        try {
            $this->validate();
            

            $tenant = Tenant::where('id',Auth::user()->id)->first();
            $startDate = Carbon::parse($this->startDate)->startOfDay();
            $endDate = Carbon::parse($this->endDate)->endOfDay();
            // dd(Auth::user(), Tenant::where('id',Auth::user()->id)->first());
            switch ($this->reportType) {
                case 'summary':
                    $this->reportData = $this->generateSummaryReport($tenant, $startDate, $endDate);
                    break;
                case 'category':
                    $this->reportData = $this->generateCategoryReport($tenant, $startDate, $endDate);
                    break;
                case 'goals':
                    $this->reportData = $this->generateGoalsReport($tenant, $startDate, $endDate);
                    break;
                case 'comparison':
                    $this->reportData = $this->generateComparisonReport($tenant, $startDate, $endDate);
                    break;
            }

        } catch (\Exception $e) {
            $this->error('Erro ao gerar relatório: ' . $e->getMessage());
        }

        $this->loading = false;
    }

    private function generateSummaryReport($tenant, $startDate, $endDate)
    {
        $query = $tenant->transactions()
            ->whereBetween('transaction_date', [$startDate, $endDate]);

        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        $transactions = $query->get();

        $totalIncome = $transactions->where('type', 'income')->sum('amount');
        $totalExpenses = $transactions->where('type', 'expense')->sum('amount');
        $netBalance = $totalIncome - $totalExpenses;

        // Transações por categoria
        $categoryBreakdown = [];
        foreach (['income', 'expense'] as $type) {
            $categoryBreakdown[$type] = $transactions
                ->where('type', $type)
                ->groupBy('category_id')
                ->map(function ($items, $categoryId) use ($tenant) {
                    $category = $tenant->categories()->find($categoryId);
                    return [
                        'category_name' => $category ? $category->name : 'Sem Categoria',
                        'category_icon' => $category ? $category->icon : 'help',
                        'category_color' => $category ? $category->color : '#6B7280',
                        'total' => $items->sum('amount'),
                        'count' => $items->count(),
                        'percentage' => $type === 'income' 
                            ? ($totalIncome > 0 ? ($items->sum('amount') / $totalIncome * 100) : 0)
                            : ($totalExpenses > 0 ? ($items->sum('amount') / $totalExpenses * 100) : 0)
                    ];
                })
                ->sortByDesc('total')
                ->values();
        }

        // Evolução mensal (últimos 6 meses)
        $monthlyEvolution = [];
        for ($i = 5; $i >= 0; $i--) {
            $monthStart = now()->subMonths($i)->startOfMonth();
            $monthEnd = now()->subMonths($i)->endOfMonth();
            
            $monthTransactions = $tenant->transactions()
                ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                ->get();

            $monthlyEvolution[] = [
                'month' => $monthStart->format('M/Y'),
                'income' => $monthTransactions->where('type', 'income')->sum('amount'),
                'expenses' => $monthTransactions->where('type', 'expense')->sum('amount'),
                'balance' => $monthTransactions->where('type', 'income')->sum('amount') - 
                           $monthTransactions->where('type', 'expense')->sum('amount')
            ];
        }

        // Top transações
        $topTransactions = $transactions
            ->sortByDesc('amount')
            ->take(10)
            ->map(function ($transaction) use ($tenant) {
                $category = $tenant->categories()->find($transaction->category_id);
                return [
                    'id' => $transaction->id,
                    'description' => $transaction->description,
                    'amount' => $transaction->amount,
                    'type' => $transaction->type,
                    'date' => $transaction->transaction_date,
                    'category_name' => $category ? $category->name : 'Sem Categoria',
                    'category_icon' => $category ? $category->icon : 'help'
                ];
            });

        return [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
                'days' => $startDate->diffInDays($endDate) + 1
            ],
            'summary' => [
                'total_income' => $totalIncome,
                'total_expenses' => $totalExpenses,
                'net_balance' => $netBalance,
                'transactions_count' => $transactions->count(),
                'daily_average_expenses' => $totalExpenses / max(1, $startDate->diffInDays($endDate) + 1),
                'savings_rate' => $totalIncome > 0 ? (($totalIncome - $totalExpenses) / $totalIncome * 100) : 0
            ],
            'category_breakdown' => $categoryBreakdown,
            'monthly_evolution' => $monthlyEvolution,
            'top_transactions' => $topTransactions
        ];
    }

    private function generateCategoryReport($tenant, $startDate, $endDate)
    {
        $categories = $tenant->categories()->get();
        $categoryData = [];

        foreach ($categories as $category) {
            $transactions = $tenant->transactions()
                ->where('category_id', $category->id)
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->get();

            if ($transactions->count() > 0) {
                $total = $transactions->sum('amount');
                
                // Evolução mensal desta categoria
                $monthlyData = [];
                for ($i = 5; $i >= 0; $i--) {
                    $monthStart = now()->subMonths($i)->startOfMonth();
                    $monthEnd = now()->subMonths($i)->endOfMonth();
                    
                    $monthTotal = $tenant->transactions()
                        ->where('category_id', $category->id)
                        ->whereBetween('transaction_date', [$monthStart, $monthEnd])
                        ->sum('amount');

                    $monthlyData[] = [
                        'month' => $monthStart->format('M/Y'),
                        'total' => $monthTotal
                    ];
                }

                $categoryData[] = [
                    'id' => $category->id,
                    'name' => $category->name,
                    'type' => $category->type,
                    'icon' => $category->icon,
                    'color' => $category->color,
                    'total' => $total,
                    'count' => $transactions->count(),
                    'average_per_transaction' => $total / $transactions->count(),
                    'monthly_evolution' => $monthlyData,
                    'recent_transactions' => $transactions->sortByDesc('transaction_date')->take(5)->map(function ($t) {
                        return [
                            'description' => $t->description,
                            'amount' => $t->amount,
                            'date' => $t->transaction_date
                        ];
                    })
                ];
            }
        }

        return collect($categoryData)->sortByDesc('total')->values();
    }

    private function generateGoalsReport($tenant, $startDate, $endDate)
    {
        $goals = $tenant->goals()
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->get();

        $goalsData = [];
        foreach ($goals as $goal) {
            $goal->calculateProgress();
            
            $category = $goal->category_id ? $tenant->categories()->find($goal->category_id) : null;
            
            $goalsData[] = [
                'id' => $goal->id,
                'name' => $goal->name,
                'type' => $goal->type,
                'category_name' => $category ? $category->name : null,
                'category_icon' => $category ? $category->icon : 'flag',
                'target_amount' => $goal->target_amount,
                'current_progress' => $goal->current_progress,
                'percentage' => $goal->progress_percentage,
                'remaining' => $goal->remaining_amount,
                'status' => $goal->status,
                'is_active' => $goal->is_active,
                'period' => $goal->period,
                'start_date' => $goal->start_date,
                'end_date' => $goal->end_date
            ];
        }

        $totalGoals = count($goalsData);
        $activeGoals = collect($goalsData)->where('is_active', true)->count();
        $completedGoals = collect($goalsData)->where('percentage', '>=', 100)->count();
        $averageProgress = $totalGoals > 0 ? collect($goalsData)->avg('percentage') : 0;

        return [
            'summary' => [
                'total_goals' => $totalGoals,
                'active_goals' => $activeGoals,
                'completed_goals' => $completedGoals,
                'average_progress' => $averageProgress
            ],
            'goals' => collect($goalsData)->sortBy('percentage')
        ];
    }

    private function generateComparisonReport($tenant, $startDate, $endDate)
    {
        // Período atual
        $currentPeriod = $this->generateSummaryReport($tenant, $startDate, $endDate);
        
        // Período anterior (mesmo intervalo)
        $daysDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $previousStart = Carbon::parse($startDate)->subDays($daysDiff + 1)->startOfDay();
        $previousEnd = Carbon::parse($startDate)->subDay()->endOfDay();
        
        $previousPeriod = $this->generateSummaryReport($tenant, $previousStart, $previousEnd);

        // Calcular diferenças
        $incomeChange = $previousPeriod['summary']['total_income'] > 0 
            ? (($currentPeriod['summary']['total_income'] - $previousPeriod['summary']['total_income']) / $previousPeriod['summary']['total_income'] * 100)
            : 0;
            
        $expenseChange = $previousPeriod['summary']['total_expenses'] > 0
            ? (($currentPeriod['summary']['total_expenses'] - $previousPeriod['summary']['total_expenses']) / $previousPeriod['summary']['total_expenses'] * 100)
            : 0;

        return [
            'current_period' => $currentPeriod,
            'previous_period' => $previousPeriod,
            'comparison' => [
                'income_change' => $incomeChange,
                'expense_change' => $expenseChange,
                'income_change_absolute' => $currentPeriod['summary']['total_income'] - $previousPeriod['summary']['total_income'],
                'expense_change_absolute' => $currentPeriod['summary']['total_expenses'] - $previousPeriod['summary']['total_expenses'],
            ]
        ];
    }

    public function exportReport($format = 'pdf')
    {
        // Implementação futura para exportação
        $this->info("Exportação em {$format} será implementada em breve!");
    }
    public function render()
    {
        $categories = Auth::user()->categories()->orderBy('name')->get();
        return view('livewire.report-manager', [
            'categories' => $categories
        ])->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle,
        ]);
    }
}
