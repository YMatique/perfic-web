<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'type',
        'category_id',
        'name',
        'target_amount',
        'period',
        'start_date',
        'end_date',
        'is_active',
        'current_progress',
        'last_calculated_at',
    ];

    protected $casts = [
        'target_amount' => 'decimal:2',
        'current_progress' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'last_calculated_at' => 'datetime',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function aiInsights()
    {
        return $this->hasMany(AiInsight::class, 'related_goal_id');
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeCurrentPeriod($query)
    {
        return $query->where('start_date', '<=', now())
            ->where(function ($q) {
                $q->whereNull('end_date')
                    ->orWhere('end_date', '>=', now());
            });
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    // Helper methods
    public function calculateProgress()
    {
        $this->current_progress = match ($this->type) {
            'spending_limit' => $this->calculateSpendingProgress(),
            'savings_target' => $this->calculateSavingsProgress(),
            'category_limit' => $this->calculateCategoryProgress(),
            'income_target' => $this->calculateIncomeProgress(),
            default => 0,
        };

        $this->last_calculated_at = now();
        $this->save();

        return $this->current_progress;
    }

    private function calculateSpendingProgress()
    {
        $periodDates = $this->getCurrentPeriodDates();

        return $this->user
            ->transactions()
            ->expense()
            ->whereBetween('transaction_date', $periodDates)
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->sum('amount');
    }

    private function calculateSavingsProgress()
    {
        $periodDates = $this->getCurrentPeriodDates();

        $income = $this->user->transactions()
            ->income()
            ->whereBetween('transaction_date', $periodDates)
            ->sum('amount');

        $expenses = $this->user->transactions()
            ->expense()
            ->whereBetween('transaction_date', $periodDates)
            ->sum('amount');

        return max(0, $income - $expenses);
    }

    private function calculateCategoryProgress()
    {
        if (!$this->category_id) return 0;

        $periodDates = $this->getCurrentPeriodDates();

        return $this->user
            ->transactions()
            ->where('category_id', $this->category_id)
            ->whereBetween('transaction_date', $periodDates)
            ->sum('amount');
    }

    private function calculateIncomeProgress()
    {
        $periodDates = $this->getCurrentPeriodDates();

        return $this->user
            ->transactions()
            ->income()
            ->whereBetween('transaction_date', $periodDates)
            ->when($this->category_id, function ($query) {
                $query->where('category_id', $this->category_id);
            })
            ->sum('amount');
    }

    private function getCurrentPeriodDates()
    {
        $start = match ($this->period) {
            'daily' => now()->startOfDay(),
            'weekly' => now()->startOfWeek(),
            'monthly' => now()->startOfMonth(),
            'quarterly' => now()->startOfQuarter(),
            'yearly' => now()->startOfYear(),
            default => $this->start_date,
        };

        $end = match ($this->period) {
            'daily' => now()->endOfDay(),
            'weekly' => now()->endOfWeek(),
            'monthly' => now()->endOfMonth(),
            'quarterly' => now()->endOfQuarter(),
            'yearly' => now()->endOfYear(),
            default => $this->end_date ?? now(),
        };

        return [$start, $end];
    }

    public function getProgressPercentageAttribute()
    {
        if ($this->target_amount == 0) return 0;
        return round(($this->current_progress / $this->target_amount) * 100, 1);
    }

    public function getRemainingAmountAttribute()
    {
        return max(0, $this->target_amount - $this->current_progress);
    }

    public function isOverTarget()
    {
        return $this->current_progress > $this->target_amount;
    }

    public function getStatusAttribute()
    {
        $percentage = $this->progress_percentage;

        return match (true) {
            $percentage >= 100 => 'completed',
            $percentage >= 80 => 'warning',
            $percentage >= 50 => 'on_track',
            default => 'below_target',
        };
    }

    public function getFormattedTargetAmountAttribute()
    {
        return 'MZN ' . number_format($this->target_amount, 2, ',', '.');
    }

    public function getFormattedCurrentProgressAttribute()
    {
        return 'MZN ' . number_format($this->current_progress, 2, ',', '.');
    }
}
