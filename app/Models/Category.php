<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
     use HasFactory;

    protected $fillable = [
        'tenant_id',
        'name',
        'type',
        'color',
        'icon',
        'is_active',
        'order',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
        'order' => 'integer',
    ];

    // Relationships
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    public function recurringTransactions()
    {
        return $this->hasMany(RecurringTransaction::class);
    }

    public function goals()
    {
        return $this->hasMany(Goal::class);
    }

    public function categorizationRules()
    {
        return $this->hasMany(CategorizationRule::class);
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
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeIncome($query)
    {
        return $query->where('type', 'income');
    }

    public function scopeExpense($query)
    {
        return $query->where('type', 'expense');
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('order')->orderBy('name');
    }

    // Helper methods
    public function getTotalSpentThisMonth()
    {
        return $this->transactions()
            ->where('type', 'expense')
            ->whereBetween('transaction_date', [
                now()->startOfMonth(),
                now()->endOfMonth()
            ])
            ->sum('amount');
    }

    public function getTransactionCount()
    {
        return $this->transactions()->count();
    }
}
