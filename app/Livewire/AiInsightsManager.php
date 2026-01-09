<?php

namespace App\Livewire;

use App\Models\AiInsight;
use App\Models\Tenant;
use App\Models\User;
use App\Services\AiAnalyzer;
use App\Traits\WithToast;
use Livewire\Component;

class AiInsightsManager extends Component
{
    use WithToast;

    public $insights = [];
    public $financialScore = null;
    public $userProfile = '';
    public $loading = false;
    public $filter = 'all';  // all, unread, high_impact
    public $title = 'Insights de IA';
    public $pageTitle = 'Insights';

    public function mount()
    {
        $this->loadInsights();
        $this->loadFinancialScore();
    }

    public function generateNewInsights()
    {
        $this->loading = true;

        try {
            $user = User::find(auth()->id());

            if (!$user) {
                $this->error('User não encontrado');
                return;
            }

            $analyzer = new AiAnalyzer();

            // Gerar novos insights
            $newInsights = $analyzer->generateInsights($user);

            // Detectar perfil do usuário
            $this->userProfile = $analyzer->detectUserProfile($user);

            $this->toastSuccess("{$newInsights->count()} novos insights gerados!");

            // Recarregar dados
            $this->loadInsights();
            $this->loadFinancialScore();
        } catch (\Exception $e) {
            $this->toastError('Erro ao gerar insights: ' . $e->getMessage());
        }

        $this->loading = false;
    }

    public function loadInsights()
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return;
        }

        $query = $user->aiInsights()->latest();

        // Aplicar filtros
        if ($this->filter === 'unread') {
            $query->where('is_read', false);
        } elseif ($this->filter === 'high_impact') {
            $query->where('impact_level', 'high');
        }

        $this->insights = $query->take(20)->get();

        // Detectar perfil
        $analyzer = new AiAnalyzer();
        $this->userProfile = $analyzer->detectUserProfile($user);
    }

    public function loadFinancialScore()
    {
        $user = User::find(auth()->id());

        if (!$user) {
            return;
        }

        $this->financialScore = $user
            ->financialScores()
            ->where('calculated_for_month', now()->format('Y-m-01'))
            ->latest('calculated_at')
            ->first();
    }

    public function markAsRead($insightId)
    {
        $insight = AiInsight::find($insightId);

        if ($insight && $insight->user_id == auth()->id()) {
            $insight->update(['is_read' => true]);
            $this->loadInsights();
        }
    }

    public function markAllAsRead()
    {
        AiInsight::where('user_id', auth()->id())
            ->where('is_read', false)
            ->update(['is_read' => true]);

        $this->success('Todos os insights marcados como lidos');
        $this->loadInsights();
    }

    public function deleteInsight($insightId)
    {
        $insight = AiInsight::find($insightId);

        if ($insight && $insight->user_id == auth()->id()) {
            $insight->delete();
            $this->success('Insight removido');
            $this->loadInsights();
        }
    }

    public function updatedFilter()
    {
        $this->loadInsights();
    }

    public function getScoreColor($score)
    {
        if ($score >= 80)
            return 'green';
        if ($score >= 60)
            return 'blue';
        if ($score >= 40)
            return 'yellow';
        return 'red';
    }

    public function getScoreMessage($score)
    {
        if ($score >= 80)
            return 'Excelente! Continue assim! 🎉';
        if ($score >= 60)
            return 'Bom trabalho! Pode melhorar um pouco. 👍';
        if ($score >= 40)
            return 'No caminho certo. Foco nas recomendações! 💪';
        return 'Precisa de atenção. Vamos melhorar juntos! 🚀';
    }

    public function getImpactIcon($level)
    {
        return match ($level) {
            'high' => 'error',
            'medium' => 'warning',
            'low' => 'info',
            default => 'help'
        };
    }

    public function getImpactColor($level)
    {
        return match ($level) {
            'high' => 'text-red-600 dark:text-red-400',
            'medium' => 'text-yellow-600 dark:text-yellow-400',
            'low' => 'text-blue-600 dark:text-blue-400',
            default => 'text-gray-600 dark:text-gray-400'
        };
    }

    public function getTypeLabel($type)
    {
        return match ($type) {
            'spending_pattern' => 'Padrão de Gasto',
            'anomaly' => 'Anomalia Detectada',
            'trend' => 'Tendência',
            'savings_opportunity' => 'Oportunidade de Economia',
            'category_concentration' => 'Concentração de Gastos',
            default => 'Insight'
        };
    }

    public function render()
    {
        return view('livewire.ai-insights-manager')->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle,
        ]);
    }
}
