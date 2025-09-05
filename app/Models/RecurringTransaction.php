<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
     use HasFactory;

    protected $fillable = [
        'tenant_id',
        'category_id',
        'type',
        'amount',
        'description',
        'frequency',
        'due_day',
        'start_date',
        'end_date',
        'is_active',
        'next_execution',
        'last_execution',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
        'next_execution' => 'datetime',
        'last_execution' => 'datetime',
        'due_day' => 'integer',
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

    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    // Global Scopes
    protected static function booted()
    {
        static::addGlobalScope('tenant', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('tenant_id', auth()->id());
            }
        });

        static::creating(function ($recurringTransaction) {
            $recurringTransaction->calculateNextExecution();
        });
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDueForExecution($query)
    {
        return $query->active()
                    ->where('next_execution', '<=', now())
                    ->where(function ($q) {
                        $q->whereNull('end_date')
                          ->orWhere('end_date', '>=', now());
                    });
    }

    // Helper methods
    public function calculateNextExecution()
    {
        if (!$this->is_active) {
            $this->next_execution = null;
            return;
        }

        $base = $this->last_execution ?? $this->start_date ?? now();
        
        $this->next_execution = match($this->frequency) {
            'daily' => $base->addDay(),
            'weekly' => $base->addWeek(),
            'monthly' => $base->addMonth(),
            'bimonthly' => $base->addMonths(2),
            'quarterly' => $base->addQuarter(),
            'yearly' => $base->addYear(),
            default => null,
        };
    }

    public function execute()
    {
        $transaction = Transaction::create([
            'tenant_id' => $this->tenant_id,
            'category_id' => $this->category_id,
            'recurring_transaction_id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->next_execution,
            'is_recurring' => true,
        ]);

        $this->last_execution = $this->next_execution;
        $this->calculateNextExecution();
        $this->save();

        return $transaction;
    }

    public function getFormattedAmountAttribute()
    {
        return 'R$ ' . number_format($this->amount, 2, ',', '.');
    }
}
