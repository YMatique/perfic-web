<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AiInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'related_category_id',
        'related_goal_id',
        'impact_value',
        'priority',
        'is_read',
        'is_actionable',
        'action_data',
        'expires_at',
        'description',
        'impact_level',
        'data',
        'category_id'
    ];

    protected $casts = [
        'impact_value' => 'decimal:2',
        'is_read' => 'boolean',
        'is_actionable' => 'boolean',
        'action_data' => 'array',
        'expires_at' => 'datetime',
         'data' => 'array',  
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function relatedCategory()
    {
        return $this->belongsTo(Category::class, 'related_category_id');
    }

    public function relatedGoal()
    {
        return $this->belongsTo(Goal::class, 'related_goal_id');
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
    public function scopeUnread($query)
    {
        return $query->where('is_read', false);
    }

    public function scopeActive($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
              ->orWhere('expires_at', '>', now());
        });
    }

    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    public function scopeHighPriority($query)
    {
        return $query->whereIn('priority', ['high', 'urgent']);
    }

    public function scopeActionable($query)
    {
        return $query->where('is_actionable', true);
    }

       public function scopeByImpactLevel($query, $level)
    {
        return $query->where('impact_level', $level);
    }

    public function scopeHighImpact($query)
    {
        return $query->where('impact_level', 'high');
    }


    // Helper methods
    public static function generateInsights(User $user)
    {
        $insights = [];
        
        // Generate goal-related insights
        $insights = array_merge($insights, static::generateGoalInsights($user));
        
        // Generate spending pattern insights
        $insights = array_merge($insights, static::generateSpendingInsights($user));
        
        // Generate savings insights
        $insights = array_merge($insights, static::generateSavingsInsights($user));
        
        // Generate anomaly insights
        $insights = array_merge($insights, static::generateAnomalyInsights($user));
        
        // Create insights in database
        foreach ($insights as $insightData) {
            static::create(array_merge([
                'user_id' => $user->id,
            ], $insightData));
        }
        
        return count($insights);
    }

    private static function generateGoalInsights(User $user)
    {
        $insights = [];
        
        $goals = $user->goals()->active()->currentPeriod()->get();
        
        foreach ($goals as $goal) {
            $goal->calculateProgress();
            $progressPercentage = $goal->progress_percentage;
            
            // Goal completion insights
            if ($progressPercentage >= 100) {
                $insights[] = [
                    'type' => 'goal_progress',
                    'title' => 'Meta Atingida! 🎉',
                    'message' => "Parabéns! Você atingiu sua meta '{$goal->name}'.",
                    'related_goal_id' => $goal->id,
                    'priority' => 'medium',
                    'is_actionable' => false,
                ];
            } elseif ($progressPercentage >= 80 && $goal->type === 'spending_limit') {
                $insights[] = [
                    'type' => 'budget_warning',
                    'title' => 'Atenção ao Orçamento ⚠️',
                    'message' => "Você já usou {$progressPercentage}% da meta '{$goal->name}'. Cuidado para não ultrapassar!",
                    'related_goal_id' => $goal->id,
                    'related_category_id' => $goal->category_id,
                    'priority' => 'high',
                    'is_actionable' => true,
                    'action_data' => [
                        'suggested_action' => 'reduce_spending',
                        'remaining_amount' => $goal->remaining_amount,
                    ],
                ];
            }
        }
        
        return $insights;
    }

    private static function generateSpendingInsights(User $user)
    {
        $insights = [];
        
        // Compare this month vs last month
        $thisMonth = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->sum('amount');

        $lastMonth = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [
                now()->subMonth()->startOfMonth(),
                now()->subMonth()->endOfMonth()
            ])
            ->sum('amount');

        if ($lastMonth > 0) {
            $changePercentage = (($thisMonth - $lastMonth) / $lastMonth) * 100;
            
            if ($changePercentage > 20) {
                $insights[] = [
                    'type' => 'spending_alert',
                    'title' => 'Gastos Aumentaram 📈',
                    'message' => "Seus gastos aumentaram {$changePercentage}% comparado ao mês passado. Valor atual: R$ " . number_format($thisMonth, 2, ',', '.'),
                    'impact_value' => $thisMonth - $lastMonth,
                    'priority' => $changePercentage > 50 ? 'urgent' : 'high',
                    'is_actionable' => true,
                    'action_data' => [
                        'suggested_action' => 'review_expenses',
                        'change_percentage' => $changePercentage,
                    ],
                ];
            } elseif ($changePercentage < -15) {
                $insights[] = [
                    'type' => 'savings_tip',
                    'title' => 'Economia Detectada! 💰',
                    'message' => "Parabéns! Você economizou " . abs($changePercentage) . "% comparado ao mês passado.",
                    'impact_value' => abs($thisMonth - $lastMonth),
                    'priority' => 'medium',
                    'is_actionable' => false,
                ];
            }
        }
        
        return $insights;
    }

    private static function generateSavingsInsights(User $user)
    {
        $insights = [];
        
        // Calculate savings rate
        $income = $user->transactions()
            ->income()
            ->whereBetween('transaction_date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->sum('amount');

        $expenses = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->sum('amount');

        if ($income > 0) {
            $savingsRate = (($income - $expenses) / $income) * 100;
            
            if ($savingsRate < 10) {
                $insights[] = [
                    'type' => 'savings_tip',
                    'title' => 'Taxa de Poupança Baixa 📊',
                    'message' => "Sua taxa de poupança está em {$savingsRate}%. Experts recomendam pelo menos 20%.",
                    'priority' => 'medium',
                    'is_actionable' => true,
                    'action_data' => [
                        'suggested_action' => 'increase_savings',
                        'current_rate' => $savingsRate,
                        'target_rate' => 20,
                    ],
                ];
            } elseif ($savingsRate > 30) {
                $insights[] = [
                    'type' => 'savings_tip',
                    'title' => 'Excelente Poupador! ⭐',
                    'message' => "Sua taxa de poupança está em {$savingsRate}%. Isso é excelente!",
                    'priority' => 'low',
                    'is_actionable' => false,
                ];
            }
        }
        
        return $insights;
    }

    private static function generateAnomalyInsights(User $user)
    {
        $insights = [];
        
        // Detect unusual spending patterns
        $recentTransactions = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [now()->subDays(7), now()])
            ->get();

        $averageTransaction = $user->transactions()
            ->expense()
            ->whereBetween('transaction_date', [now()->subDays(90), now()->subDays(7)])
            ->avg('amount');

        if ($averageTransaction) {
            foreach ($recentTransactions as $transaction) {
                if ($transaction->amount > ($averageTransaction * 3)) {
                    $insights[] = [
                        'type' => 'anomaly_detected',
                        'title' => 'Gasto Incomum Detectado 🔍',
                        'message' => "Detectamos um gasto de R$ {$transaction->formatted_amount} em {$transaction->category->name}, que é 3x maior que sua média.",
                        'related_category_id' => $transaction->category_id,
                        'impact_value' => $transaction->amount,
                        'priority' => 'medium',
                        'is_actionable' => true,
                        'action_data' => [
                            'suggested_action' => 'review_transaction',
                            'transaction_id' => $transaction->id,
                        ],
                    ];
                }
            }
        }
        
        return $insights;
    }

    public function markAsRead()
    {
        $this->update(['is_read' => true]);
    }

    public function isExpired()
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function getPriorityColorAttribute()
    {
        return match($this->priority) {
            'urgent' => '#DC2626', // red-600
            'high' => '#EA580C',   // orange-600
            'medium' => '#D97706', // amber-600
            'low' => '#059669',    // emerald-600
            default => '#6B7280',  // gray-500
        };
    }

    public function getPriorityIconAttribute()
    {
        return match($this->priority) {
            'urgent' => '🚨',
            'high' => '⚠️',
            'medium' => '💡',
            'low' => 'ℹ️',
            default => '📝',
        };
    }
    public function getImpactColorAttribute()
    {
        return match($this->impact_level) {
            'high' => '#DC2626',   // red-600
            'medium' => '#D97706', // amber-600
            'low' => '#059669',    // emerald-600
            default => '#6B7280',  // gray-500
        };
    }

    public function getImpactIconAttribute()
    {
        return match($this->impact_level) {
            'high' => 'error',
            'medium' => 'warning',
            'low' => 'info',
            default => 'help',
        };
    }
    
}
