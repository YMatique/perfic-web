<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Tenant extends Model
{
    use  HasFactory, Notifiable, HasUuids;
// HasApiTokens,
    protected $fillable = [
        'name',
        'email',
        'password',
        'settings',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'settings' => 'array',
    ];

    // Relationships
    public function categories()
    {
        return $this->hasMany(Category::class);
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

    public function financialScores()
    {
        return $this->hasMany(FinancialScore::class);
    }

    public function aiInsights()
    {
        return $this->hasMany(AiInsight::class);
    }

    public function behaviorPatterns()
    {
        return $this->hasMany(BehaviorPattern::class);
    }

    public function categorizationRules()
    {
        return $this->hasMany(CategorizationRule::class);
    }

    public function localBackups()
    {
        return $this->hasMany(LocalBackup::class);
    }

    public function monthlySummaries()
    {
        return $this->hasMany(MonthlySummary::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Helper methods
    public function getCurrentFinancialScore()
    {
        return $this->financialScores()
            ->where('calculated_for_month', now()->format('Y-m-01'))
            ->latest('calculated_at')
            ->first()?->score ?? 0;
    }

    public function getUnreadInsightsCount()
    {
        return $this->aiInsights()->where('is_read', false)->count();
    }
}
