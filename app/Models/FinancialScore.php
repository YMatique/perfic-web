<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialScore extends Model
{
     use HasFactory;

    protected $fillable = [
        'tenant_id',
        'score',
        'score_breakdown',
        'calculated_for_month',
        'calculated_at',
    ];

    protected $casts = [
        'score' => 'decimal:2',
        'score_breakdown' => 'array',
        'calculated_for_month' => 'date',
        'calculated_at' => 'datetime',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    // Global Scopes
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->id());
            }
        });
    }

    // Scopes
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('calculated_for_month', $year)
                    ->whereMonth('calculated_for_month', $month);
    }

    public function scopeLatest($query)
    {
        return $query->orderBy('calculated_for_month', 'desc')
                    ->orderBy('calculated_at', 'desc');
    }

    // Helper methods
    public static function calculateForTenant(Tenant $tenant, $month = null)
    {
        $month = $month ?? now()->startOfMonth();
        
        $score = new static();
        $score->tenant_id = $tenant->id;
        $score->calculated_for_month = $month;
        $score->calculated_at = now();
        
        $breakdown = $score->calculateScoreBreakdown($tenant, $month);
        $score->score_breakdown = $breakdown;
        $score->score = $breakdown['total_score'];
        
        $score->save();
        
        return $score;
    }

    private function calculateScoreBreakdown(Tenant $tenant, $month)
    {
        $startOfMonth = $month->copy()->startOfMonth();
        $endOfMonth = $month->copy()->endOfMonth();

        // 1. Budget Adherence (40 points)
        $budgetScore = $this->calculateBudgetAdherence($tenant, $startOfMonth, $endOfMonth);
        
        // 2. Savings Rate (30 points)
        $savingsScore = $this->calculateSavingsRate($tenant, $startOfMonth, $endOfMonth);
        
        // 3. Spending Consistency (20 points)
        $consistencyScore = $this->calculateSpendingConsistency($tenant, $startOfMonth, $endOfMonth);
        
        // 4. App Usage (10 points)
        $usageScore = $this->calculateAppUsage($tenant, $startOfMonth, $endOfMonth);

        return [
            'budget_adherence' => $budgetScore,
            'savings_rate' => $savingsScore,
            'spending_consistency' => $consistencyScore,
            'app_usage' => $usageScore,
            'total_score' => $budgetScore + $savingsScore + $consistencyScore + $usageScore,
            'calculation_details' => [
                'month' => $month->format('Y-m'),
                'calculated_at' => now()->toISOString(),
            ]
        ];
    }

    private function calculateBudgetAdherence($tenant, $start, $end)
    {
        $activeGoals = $tenant->goals()
            ->active()
            ->where('type', 'spending_limit')
            ->currentPeriod()
            ->get();

        if ($activeGoals->isEmpty()) return 20; // Default score if no goals

        $totalAdherence = 0;
        foreach ($activeGoals as $goal) {
            $goal->calculateProgress();
            $adherence = min(100, ($goal->target_amount / max($goal->current_progress, 1)) * 100);
            $totalAdherence += $adherence;
        }

        $averageAdherence = $totalAdherence / $activeGoals->count();
        return round(($averageAdherence / 100) * 40);
    }

    private function calculateSavingsRate($tenant, $start, $end)
    {
        $income = $tenant->transactions()
            ->income()
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('amount');

        $expenses = $tenant->transactions()
            ->expense()
            ->whereBetween('transaction_date', [$start, $end])
            ->sum('amount');

        if ($income == 0) return 0;

        $savingsRate = (($income - $expenses) / $income) * 100;
        $savingsRate = max(0, min(100, $savingsRate));

        return round(($savingsRate / 100) * 30);
    }

    private function calculateSpendingConsistency($tenant, $start, $end)
    {
        // Calculate daily spending variance
        $dailySpending = $tenant->transactions()
            ->expense()
            ->whereBetween('transaction_date', [$start, $end])
            ->selectRaw('DATE(transaction_date) as date, SUM(amount) as total')
            ->groupBy('date')
            ->pluck('total')
            ->toArray();

        if (count($dailySpending) < 7) return 10; // Default if insufficient data

        $mean = array_sum($dailySpending) / count($dailySpending);
        $variance = 0;
        foreach ($dailySpending as $spending) {
            $variance += pow($spending - $mean, 2);
        }
        $variance /= count($dailySpending);
        $stdDev = sqrt($variance);

        $consistencyScore = max(0, 100 - ($stdDev / $mean * 100));
        return round(($consistencyScore / 100) * 20);
    }

    private function calculateAppUsage($tenant, $start, $end)
    {
        $transactionCount = $tenant->transactions()
            ->whereBetween('created_at', [$start, $end])
            ->count();

        $goalUsage = $tenant->goals()->active()->count() > 0 ? 3 : 0;
        $categoryUsage = $tenant->categories()->active()->count() >= 5 ? 2 : 0;
        $recurringUsage = $tenant->recurringTransactions()->active()->count() > 0 ? 2 : 0;
        $transactionUsage = min(3, $transactionCount / 10); // 1 point per 10 transactions, max 3

        return $goalUsage + $categoryUsage + $recurringUsage + $transactionUsage;
    }

    public function getScoreGradeAttribute()
    {
        return match(true) {
            $this->score >= 90 => 'A+',
            $this->score >= 80 => 'A',
            $this->score >= 70 => 'B',
            $this->score >= 60 => 'C',
            $this->score >= 50 => 'D',
            default => 'F',
        };
    }

    public function getScoreColorAttribute()
    {
        return match(true) {
            $this->score >= 80 => '#10B981', // green
            $this->score >= 60 => '#F59E0B', // yellow
            default => '#EF4444', // red
        };
    }
}
