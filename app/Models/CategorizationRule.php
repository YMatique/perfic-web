<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategorizationRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'keyword',
        'category_id',
        'confidence',
        'rule_type',
        'rule_data',
        'usage_count',
        'success_count',
        'is_active',
        'is_auto_generated',
    ];

    protected $casts = [
        'confidence' => 'decimal:2',
        'usage_count' => 'integer',
        'success_count' => 'integer',
        'is_active' => 'boolean',
        'is_auto_generated' => 'boolean',
        'rule_data' => 'array',
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

    public function scopeHighConfidence($query)
    {
        return $query->where('confidence', '>=', 0.7);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('rule_type', $type);
    }

    public function scopeByKeyword($query, $keyword)
    {
        return $query->where('keyword', 'LIKE', "%{$keyword}%");
    }

    // Helper methods
    public static function suggestCategory(User $user, $description, $amount = null, $location = null)
    {
        $description = strtolower(trim($description));
        $suggestions = [];

        // Find matching rules
        $rules = static::where('user_id', $user->id)
            ->active()
            ->orderBy('confidence', 'desc')
            ->orderBy('success_count', 'desc')
            ->get();

        foreach ($rules as $rule) {
            $match = static::checkRuleMatch($rule, $description, $amount, $location);

            if ($match['matches']) {
                $suggestions[] = [
                    'category_id' => $rule->category_id,
                    'category' => $rule->category,
                    'confidence' => $match['confidence'],
                    'rule_id' => $rule->id,
                    'reason' => $match['reason'],
                ];

                // Update usage count
                $rule->increment('usage_count');
            }
        }

        // Sort by confidence and return top 3
        usort($suggestions, fn($a, $b) => $b['confidence'] <=> $a['confidence']);

        return array_slice($suggestions, 0, 3);
    }

    private static function checkRuleMatch($rule, $description, $amount, $location)
    {
        $matches = false;
        $confidence = $rule->confidence;
        $reason = '';

        switch ($rule->rule_type) {
            case 'keyword':
                if (str_contains($description, strtolower($rule->keyword))) {
                    $matches = true;
                    $reason = "Palavra-chave: '{$rule->keyword}'";
                }
                break;

            case 'regex':
                if (preg_match($rule->keyword, $description)) {
                    $matches = true;
                    $reason = "Padrão regex detectado";
                }
                break;

            case 'amount_range':
                $range = $rule->rule_data['amount_range'] ?? [];
                if ($amount && isset($range['min']) && isset($range['max'])) {
                    if ($amount >= $range['min'] && $amount <= $range['max']) {
                        $matches = true;
                        $reason = "Valor entre R$ {$range['min']} e R$ {$range['max']}";
                        // Lower confidence for amount-only matches
                        $confidence *= 0.7;
                    }
                }
                break;

            case 'location':
                if ($location && str_contains(strtolower($location), strtolower($rule->keyword))) {
                    $matches = true;
                    $reason = "Localização: '{$rule->keyword}'";
                }
                break;

            case 'merchant':
                $merchantNames = $rule->rule_data['merchant_names'] ?? [];
                foreach ($merchantNames as $merchantName) {
                    if (str_contains($description, strtolower($merchantName))) {
                        $matches = true;
                        $reason = "Estabelecimento: '{$merchantName}'";
                        break;
                    }
                }
                break;
        }

        return [
            'matches' => $matches,
            'confidence' => $matches ? $confidence : 0,
            'reason' => $reason,
        ];
    }

    public static function learnFromTransaction(Transaction $transaction, $userSelectedCategory = null)
    {
        $user = $transaction->user;
        $description = strtolower(trim($transaction->description ?? ''));

        if (empty($description)) return;

        $categoryId = $userSelectedCategory ?? $transaction->category_id;

        // Extract keywords from description
        $keywords = static::extractKeywords($description);

        foreach ($keywords as $keyword) {
            // Check if rule already exists
            $existingRule = static::where('user_id', $user->id)
                ->where('keyword', $keyword)
                ->where('category_id', $categoryId)
                ->where('rule_type', 'keyword')
                ->first();

            if ($existingRule) {
                // Update existing rule
                $existingRule->increment('success_count');
                $existingRule->confidence = min(0.95, $existingRule->confidence + 0.1);
                $existingRule->save();
            } else {
                // Create new rule
                static::create([
                    'user_id' => $user->id,
                    'keyword' => $keyword,
                    'category_id' => $categoryId,
                    'confidence' => 0.6,
                    'rule_type' => 'keyword',
                    'usage_count' => 1,
                    'success_count' => 1,
                    'is_active' => true,
                    'is_auto_generated' => true,
                ]);
            }
        }
    }

    private static function extractKeywords($description)
    {
        // Remove common words and extract meaningful keywords
        $stopWords = ['de', 'da', 'do', 'em', 'na', 'no', 'para', 'com', 'por', 'the', 'and', 'or', 'but', 'in', 'on', 'at', 'to', 'for', 'of', 'with', 'by'];

        $words = preg_split('/[\s\-\.,;:!?]+/', $description);
        $keywords = [];

        foreach ($words as $word) {
            $word = trim($word);
            if (strlen($word) >= 3 && !in_array(strtolower($word), $stopWords)) {
                $keywords[] = strtolower($word);
            }
        }

        // Also include multi-word phrases
        if (count($words) >= 2) {
            for ($i = 0; $i < count($words) - 1; $i++) {
                $phrase = strtolower(trim($words[$i] . ' ' . $words[$i + 1]));
                if (strlen($phrase) >= 6) {
                    $keywords[] = $phrase;
                }
            }
        }

        return array_unique($keywords);
    }

    public function recordSuccess()
    {
        $this->increment('success_count');
        $this->confidence = min(0.95, $this->confidence + 0.05);
        $this->save();
    }

    public function recordFailure()
    {
        $this->confidence = max(0.1, $this->confidence - 0.1);

        if ($this->confidence < 0.3) {
            $this->is_active = false;
        }

        $this->save();
    }

    public function getSuccessRateAttribute()
    {
        return $this->usage_count > 0 ? ($this->success_count / $this->usage_count) * 100 : 0;
    }

    public function getConfidenceScoreAttribute()
    {
        return round($this->confidence * 100, 1) . '%';
    }
}
