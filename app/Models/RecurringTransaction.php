<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RecurringTransaction extends Model
{
     use HasFactory;

    protected $fillable = [
        'user_id',
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
    public function user()
    {
        return $this->belongsTo(User::class);
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
        static::addGlobalScope('user', function (Builder $builder) {
            if (auth()->check()) {
                $builder->where('user_id', auth()->id());
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
        // If we have a due_day, adjust to that specific day
        if ($this->due_day && $this->next_execution) {
            if ($this->frequency === 'weekly') {
                // For weekly, due_day is day of week (1=Monday, 7=Sunday)
                $this->next_execution = $this->next_execution->startOfWeek()->addDays($this->due_day - 1);
            } elseif (in_array($this->frequency, ['monthly', 'bimonthly', 'quarterly', 'yearly'])) {
                // For monthly/quarterly/yearly, due_day is day of month (1-31)
                $this->next_execution = $this->next_execution->day(min($this->due_day, $this->next_execution->daysInMonth));
            }
        }

        // Make sure we don't go past end_date
        if ($this->end_date && $this->next_execution->isAfter($this->end_date)) {
            $this->next_execution = null;
            $this->is_active = false;
        }
    }

    public function execute()
    {
        $transaction = Transaction::create([
            'user_id' => $this->user_id,
            'category_id' => $this->category_id,
            'recurring_transaction_id' => $this->id,
            'type' => $this->type,
            'amount' => $this->amount,
            'description' => $this->description,
            'transaction_date' => $this->next_execution,
            'is_recurring' => true,
        ]);

       
        $this->last_execution = $this->next_execution ?? now();
        $this->calculateNextExecution();
        $this->save();

        return $transaction;
    }

    public function getFormattedAmountAttribute()
    {
        return 'MZN ' . number_format($this->amount, 2, ',', '.');
    }
}
