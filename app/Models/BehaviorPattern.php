<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BehaviorPattern extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
        'pattern_type',
        'pattern_key',
        'average_value',
        'frequency',
        'confidence',
        'pattern_data',
        'calculated_at',
    ];

    protected $casts = [
        'average_value' => 'decimal:2',
        'frequency' => 'integer',
        'confidence' => 'decimal:2',
        'pattern_data' => 'array',
        'calculated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Global Scopes
    protected static function booted()
    {
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
            }
        });
    }

    // Scopes
    public function scopeHighConfidence($query)
    {
        return $query->where('confidence', '>=', 0.7);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('pattern_type', $type);
    }

    public function scopeRecent($query)
    {
        return $query->where('calculated_at', '>=', now()->subDays(30));
    }

    // Helper methods
    public static function analyzePatterns(User $user)
    {
        $patterns = [];
        
        // Analyze spending patterns
        $patterns = array_merge($patterns, static::analyzeSpendingPatterns($user));
        
        // Analyze temporal patterns
        $patterns = array_merge($patterns, static::analyzeTemporalPatterns($user));
        
        // Analyze category patterns
        $patterns = array_merge($patterns, static::analyzeCategoryPatterns($user));
        
        return $patterns;
    }

    private static function analyzeSpendingPatterns(User $user)
    {
        $patterns = [];
        
        // Weekend vs Weekday spending
        $weekendSpending = $user->transactions()
            ->expense()
            ->whereRaw('DAYOFWEEK(transaction_date) IN (1, 7)') // Sunday, Saturday
            ->whereBetween('transaction_date', [now()->subDays(90), now()])
            ->avg('amount');

        $weekdaySpending = $user->transactions()
            ->expense()
            ->whereRaw('DAYOFWEEK(transaction_date) NOT IN (1, 7)')
            ->whereBetween('transaction_date', [now()->subDays(90), now()])
            ->avg('amount');

        if ($weekendSpending && $weekdaySpending) {
            $ratio = $weekendSpending / $weekdaySpending;
            if ($ratio > 1.5) {
                $patterns[] = [
                    'pattern_type' => 'spending',
                    'pattern_key' => 'weekend_spender',
                    'average_value' => $weekendSpending,
                    'confidence' => min(0.95, $ratio / 2),
                    'pattern_data' => [
                        'weekend_avg' => $weekendSpending,
                        'weekday_avg' => $weekdaySpending,
                        'ratio' => $ratio,
                    ]
                ];
            }
        }

        return $patterns;
    }

    private static function analyzeTemporalPatterns(User $user)
    {
        $patterns = [];
        
        // Monthly spending trend
        $monthlySpending = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [now()->subMonths(6), now()])
            ->selectRaw('YEAR(transaction_date) as year, MONTH(transaction_date) as month, SUM(amount) as total')
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        if ($monthlySpending->count() >= 3) {
            $amounts = $monthlySpending->pluck('total')->toArray();
            $trend = static::calculateTrend($amounts);
            
            if (abs($trend) > 0.1) {
                $patterns[] = [
                    'pattern_type' => 'temporal',
                    'pattern_key' => $trend > 0 ? 'increasing_spending' : 'decreasing_spending',
                    'average_value' => array_sum($amounts) / count($amounts),
                    'confidence' => min(0.9, abs($trend)),
                    'pattern_data' => [
                        'trend_coefficient' => $trend,
                        'monthly_data' => $amounts,
                    ]
                ];
            }
        }

        return $patterns;
    }

    private static function analyzeCategoryPatterns(User $user)
    {
        $patterns = [];
        
        // Find dominant spending categories
        $categorySpending = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [now()->subDays(90), now()])
            ->selectRaw('category_id, SUM(amount) as total, COUNT(*) as count')
            ->groupBy('category_id')
            ->having('count', '>=', 5)
            ->orderBy('total', 'desc')
            ->with('category')
            ->get();

        $totalSpending = $categorySpending->sum('total');
        
        foreach ($categorySpending as $spending) {
            $percentage = ($spending->total / $totalSpending) * 100;
            
            if ($percentage > 30) {
                $patterns[] = [
                    'pattern_type' => 'category',
                    'pattern_key' => 'dominant_category_' . $spending->category_id,
                    'average_value' => $spending->total / $spending->count,
                    'confidence' => min(0.9, $percentage / 50),
                    'pattern_data' => [
                        'category_name' => $spending->category->name,
                        'total_amount' => $spending->total,
                        'percentage' => $percentage,
                        'transaction_count' => $spending->count,
                    ]
                ];
            }
        }

        return $patterns;
    }

    private static function calculateTrend($data)
    {
        $n = count($data);
        if ($n < 2) return 0;
        
        $x = range(1, $n);
        $sumX = array_sum($x);
        $sumY = array_sum($data);
        $sumXY = 0;
        $sumXX = 0;
        
        for ($i = 0; $i < $n; $i++) {
            $sumXY += $x[$i] * $data[$i];
            $sumXX += $x[$i] * $x[$i];
        }
        
        return ($n * $sumXY - $sumX * $sumY) / ($n * $sumXX - $sumX * $sumX);
    }

    public function getDescriptionAttribute()
    {
        return match($this->pattern_key) {
            'weekend_spender' => 'Você tende a gastar mais nos fins de semana',
            'increasing_spending' => 'Seus gastos têm aumentado ao longo do tempo',
            'decreasing_spending' => 'Seus gastos têm diminuído ao longo do tempo',
            default => 'Padrão de comportamento detectado: ' . $this->pattern_key,
        };
    }
}
