<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn($word) => Str::substr($word, 0, 1))
            ->implode('');
    }

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

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNotNull('email_verified_at');
    }

    // Helper methods
    public function getCurrentFinancialScore(): int
    {
        return $this
            ->financialScores()
            ->where('calculated_for_month', now()->format('Y-m-01'))
            ->latest('calculated_at')
            ->first()
            ?->score ?? 0;
    }

    public function getUnreadInsightsCount(): int
    {
        return $this->aiInsights()->where('is_read', false)->count();
    }
}
