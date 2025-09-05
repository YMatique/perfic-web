<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'recurring_transaction_id',
        'type',
        'amount',
        'description',
        'transaction_date',
        'is_recurring',
        'categorized_by_ai',
        'ai_confidence',
        'location',
        'attachments',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'transaction_date' => 'datetime',
        'is_recurring' => 'boolean',
        'categorized_by_ai' => 'boolean',
        'ai_confidence' => 'decimal:2',
        'attachments' => 'array',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function recurringTransaction()
    {
        return $this->belongsTo(RecurringTransaction::class);
    }

    // Global Scopes
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->id());
            }
        });

        // Log when transaction is created/updated
        static::created(function ($transaction) {
            \Log::info('Transaction created', [
                'tenant_id' => $transaction->tenant_id,
                'id' => $transaction->id,
                'amount' => $transaction->amount,
                'type' => $transaction->type,
            ]);
        });
    }

    // Scopes
    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeThisMonth($query)
    {
        return $query->whereBetween('transaction_date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    public function scopeThisYear($query)
    {
        return $query->whereBetween('transaction_date', [
            now()->startOfYear(),
            now()->endOfYear()
        ]);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('transaction_date', 'desc')
                    ->orderBy('created_at', 'desc');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    // Helper methods
    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }

    public function isExpense()
    {
        return $this->type === 'expense';
    }

    public function isIncome()
    {
        return $this->type === 'income';
    }
}
