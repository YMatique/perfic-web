<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Goal;
use App\Traits\WithToast;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class GoalManager extends Component
{
    use WithPagination, WithToast;

    public $title = 'Metas Financeiras';

    public $pageTitle = 'Gestão de Metas';

    // Form properties
    #[Rule('required|string|max:255')]
    public $name = '';

    #[Rule('required|in:spending_limit,savings_target,category_limit,income_target')]
    public $type = 'spending_limit';

    #[Rule('nullable|exists:categories,id')]
    public $category_id = '';

    #[Rule('required|numeric|min:0.01')]
    public $target_amount = '';

    #[Rule('required|in:daily,weekly,monthly,quarterly,yearly')]
    public $period = 'monthly';

    #[Rule('required|date')]
    public $start_date = '';

    #[Rule('nullable|date|after:start_date')]
    public $end_date = '';

    #[Rule('boolean')]
    public $is_active = true;

    // Component state
    public $goals;

    public $categories;

    public $showForm = false;

    public $editingGoal = null;

    // Filters
    public $filterType = 'all';

    public $filterStatus = 'all';

    public $filterPeriod = 'all';

    // Goal types for display
    public $goalTypes = [
        'spending_limit' => 'Limite de Gastos',
        'savings_target' => 'Meta de Poupança',
        'category_limit' => 'Limite por Categoria',
        'income_target' => 'Meta de Receita',
    ];

    public $periods = [
        'daily' => 'Diário',
        'weekly' => 'Semanal',
        'monthly' => 'Mensal',
        'quarterly' => 'Trimestral',
        'yearly' => 'Anual',
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->loadGoals();
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
    }

    public function loadCategories()
    {
        $this->categories = Category::active()->ordered()->get();
    }

    public function loadGoals()
    {
        $query = Goal::with(['category'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'active') {
                $query->where('is_active', true);
            } elseif ($this->filterStatus === 'inactive') {
                $query->where('is_active', false);
            } elseif ($this->filterStatus === 'completed') {
                $query->where('is_active', true)
                    ->whereRaw('current_progress >= target_amount');
            } elseif ($this->filterStatus === 'warning') {
                $query->where('is_active', true)
                    ->whereRaw('(current_progress / target_amount) >= 0.8')
                    ->whereRaw('current_progress < target_amount');
            }
        }

        if ($this->filterPeriod !== 'all') {
            $query->where('period', $this->filterPeriod);
        }

        $this->goals = $query->get();

        // Calculate progress for each goal
        foreach ($this->goals as $goal) {
            $goal->calculateProgress();
        }
    }

    public function updatedFilterType()
    {
        $this->loadGoals();
    }

    public function updatedFilterStatus()
    {
        $this->loadGoals();
    }

    public function updatedFilterPeriod()
    {
        $this->loadGoals();
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function showEditForm(Goal $goal)
    {
        $this->editingGoal = $goal;
        $this->name = $goal->name;
        $this->type = $goal->type;
        $this->category_id = $goal->category_id;
        $this->target_amount = $goal->target_amount;
        $this->period = $goal->period;
        $this->start_date = $goal->start_date->format('Y-m-d');
        $this->end_date = $goal->end_date?->format('Y-m-d') ?? '';
        $this->is_active = $goal->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'user_id' => auth()->id(),
            'name' => $this->name,
            'type' => $this->type,
            'category_id' => $this->category_id ?: null,
            'target_amount' => $this->target_amount,
            'period' => $this->period,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingGoal) {
            $this->editingGoal->update($data);
            $this->success('Meta atualizada com sucesso!');
        } else {
            Goal::create($data);
            $this->success('Meta criada com sucesso!');
        }

        $this->cancelForm();
        $this->loadGoals();
    }

    public function delete(Goal $goal)
    {
        $goal->delete();
        $this->success('Meta excluída com sucesso!');
        $this->loadGoals();
    }

    public function toggleStatus(Goal $goal)
    {
        $goal->update(['is_active' => ! $goal->is_active]);
        $status = $goal->is_active ? 'ativada' : 'desativada';
        $this->success("Meta {$status} com sucesso!");
        $this->loadGoals();
    }

    public function calculateProgress(Goal $goal)
    {
        $goal->calculateProgress();
        $this->success('Progresso recalculado!');
        $this->loadGoals();
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingGoal = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'type', 'category_id', 'target_amount',
            'period', 'start_date', 'end_date', 'is_active',
        ]);
        $this->start_date = now()->startOfMonth()->format('Y-m-d');
        $this->end_date = now()->endOfMonth()->format('Y-m-d');
        $this->type = 'spending_limit';
        $this->period = 'monthly';
        $this->is_active = true;
    }

    public function getGoalStatusColor($goal)
    {
        if (! $goal->is_active) {
            return 'gray';
        }

        $percentage = $goal->progress_percentage;

        return match (true) {
            $percentage >= 100 => 'green',
            $percentage >= 80 => 'yellow',
            $percentage >= 50 => 'blue',
            default => 'gray'
        };
    }

    public function getGoalStatusIcon($goal)
    {
        if (! $goal->is_active) {
            return 'pause_circle';
        }

        $percentage = $goal->progress_percentage;

        return match (true) {
            $percentage >= 100 => 'check_circle',
            $percentage >= 80 => 'warning',
            $percentage >= 50 => 'trending_up',
            default => 'radio_button_unchecked'
        };
    }

    public function getGoalStatusText($goal)
    {
        if (! $goal->is_active) {
            return 'Inativa';
        }

        $percentage = $goal->progress_percentage;

        return match (true) {
            $percentage >= 100 => 'Concluída',
            $percentage >= 80 => 'Atenção',
            $percentage >= 50 => 'No Caminho',
            default => 'Abaixo da Meta'
        };
    }

    public function render()
    {
        // Process goals to add status properties
        if ($this->goals) {
            foreach ($this->goals as $goal) {
                $goal->status_color = $this->getGoalStatusColor($goal);
                $goal->status_icon = $this->getGoalStatusIcon($goal);
                $goal->status_text = $this->getGoalStatusText($goal);
            }
        }

        return view('livewire.goal-manager')->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle,
        ]);
    }
}
