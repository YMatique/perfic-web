<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\RecurringTransaction;
use App\Traits\WithToast;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class RecurringTransactionManager extends Component
{
    use WithToast, WithPagination;

    public $title = 'Transações Recorrentes';
    public $pageTitle = 'Gestão de Transações Recorrentes';

    // Form properties
    #[Rule('required|string|max:255')]
    public $description = '';

    #[Rule('required|numeric|min:0.01')]
    public $amount = '';

    #[Rule('required|in:income,expense')]
    public $type = 'expense';

    #[Rule('required|exists:categories,id')]
    public $category_id = '';

    #[Rule('required|in:daily,weekly,monthly,bimonthly,quarterly,yearly')]
    public $frequency = 'monthly';

    #[Rule('nullable|integer|min:1|max:31')]
    public $due_day = '';

    #[Rule('required|date')]
    public $start_date = '';

    #[Rule('nullable|date|after:start_date')]
    public $end_date = '';

    #[Rule('boolean')]
    public $is_active = true;

    // Component state
    public $recurringTransactions;
    public $categories;
    public $showForm = false;
    public $editingTransaction = null;
    
    // Filters
    public $filterType = 'all';
    public $filterFrequency = 'all';
    public $filterStatus = 'all';

    // Frequency options
    public $frequencies = [
        'daily' => 'Diário',
        'weekly' => 'Semanal',
        'monthly' => 'Mensal',
        'bimonthly' => 'Bimestral',
        'quarterly' => 'Trimestral',
        'yearly' => 'Anual'
    ];

    public function mount()
    {
        $this->loadCategories();
        $this->loadRecurringTransactions();
        $this->start_date = now()->format('Y-m-d');
        $this->due_day = now()->day;
    }

    public function loadCategories()
    {
        $this->categories = Category::active()->ordered()->get();
    }

    public function loadRecurringTransactions()
    {
        $query = RecurringTransaction::with(['category'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterFrequency !== 'all') {
            $query->where('frequency', $this->filterFrequency);
        }

        if ($this->filterStatus !== 'all') {
            if ($this->filterStatus === 'active') {
                $query->where('is_active', true);
            } elseif ($this->filterStatus === 'inactive') {
                $query->where('is_active', false);
            } elseif ($this->filterStatus === 'due') {
                $query->where('is_active', true)
                     ->where('next_execution', '<=', now());
            }
        }

        $this->recurringTransactions = $query->get();
    }

    public function updatedFilterType()
    {
        $this->loadRecurringTransactions();
    }

    public function updatedFilterFrequency()
    {
        $this->loadRecurringTransactions();
    }

    public function updatedFilterStatus()
    {
        $this->loadRecurringTransactions();
    }

    public function updatedFrequency()
    {
        // Update due_day based on frequency
        $this->due_day = match($this->frequency) {
            'daily' => null,
            'weekly' => now()->dayOfWeek, // 1-7 (Monday = 1)
            'monthly', 'bimonthly', 'quarterly', 'yearly' => now()->day, // 1-31
            default => null,
        };
    }

    public function showCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function showEditForm(RecurringTransaction $recurringTransaction)
    {
        $this->editingTransaction = $recurringTransaction;
        $this->description = $recurringTransaction->description;
        $this->amount = $recurringTransaction->amount;
        $this->type = $recurringTransaction->type;
        $this->category_id = $recurringTransaction->category_id;
        $this->frequency = $recurringTransaction->frequency;
        $this->due_day = $recurringTransaction->due_day;
        $this->start_date = $recurringTransaction->start_date->format('Y-m-d');
        $this->end_date = $recurringTransaction->end_date?->format('Y-m-d') ?? '';
        $this->is_active = $recurringTransaction->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'tenant_id' => auth()->id(),
            'description' => $this->description,
            'amount' => $this->amount,
            'type' => $this->type,
            'category_id' => $this->category_id,
            'frequency' => $this->frequency,
            'due_day' => $this->due_day,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date ?: null,
            'is_active' => $this->is_active,
        ];

        if ($this->editingTransaction) {
            $this->editingTransaction->update($data);
            $this->editingTransaction->calculateNextExecution();
            $this->editingTransaction->save();
            $this->toastSuccess('Sucesso','Transação recorrente atualizada com sucesso!');
        } else {
            RecurringTransaction::create($data);
            $this->toastSuccess('Sucesso','Transação recorrente criada com sucesso!');
        }

        $this->cancelForm();
        $this->loadRecurringTransactions();
    }

    public function delete(RecurringTransaction $recurringTransaction)
    {
        $recurringTransaction->delete();
        $this->toastSuccess('Sucesso','Transação recorrente excluída com sucesso!');
        $this->loadRecurringTransactions();
    }

    public function toggleStatus(RecurringTransaction $recurringTransaction)
    {
        $recurringTransaction->update(['is_active' => !$recurringTransaction->is_active]);
        
        if ($recurringTransaction->is_active) {
            $recurringTransaction->calculateNextExecution();
            $recurringTransaction->save();
        }
        
        $status = $recurringTransaction->is_active ? 'ativada' : 'desativada';
        $this->toastSuccess('Sucesso',"Transação recorrente {$status} com sucesso!");
        $this->loadRecurringTransactions();
    }

    public function executeNow(RecurringTransaction $recurringTransaction)
    {
        if (!$recurringTransaction->is_active) {
            $this->error('Não é possível executar uma transação inativa.');
            return;
        }

        try {
            $transaction = $recurringTransaction->execute();
            $this->toastSuccess('Sucesso',"Transação executada! Valor: {$transaction->formatted_amount}");
            $this->loadRecurringTransactions();
        } catch (\Exception $e) {
            $this->error('Erro ao executar transação: ' . $e->getMessage());
        }
    }

    public function executeAllDue()
    {
        $dueTransactions = RecurringTransaction::dueForExecution()->get();
        $executed = 0;
        $errors = 0;

        foreach ($dueTransactions as $recurring) {
            try {
                $recurring->execute();
                $executed++;
            } catch (\Exception $e) {
                $errors++;
            }
        }

        if ($executed > 0) {
            $this->toastSuccess('Sucesso',"$executed transação(ões) executada(s) com sucesso!");
        }
        
        if ($errors > 0) {
            $this->warning("$errors transação(ões) falharam na execução.");
        }

        if ($executed === 0 && $errors === 0) {
            $this->info('Nenhuma transação estava pendente de execução.');
        }

        $this->loadRecurringTransactions();
    }

    public function cancelForm()
    {
        $this->showForm = false;
        $this->editingTransaction = null;
        $this->resetForm();
    }

    public function resetForm()
    {
        $this->reset([
            'description', 'amount', 'type', 'category_id', 
            'frequency', 'due_day', 'start_date', 'end_date', 'is_active'
        ]);
        $this->start_date = now()->format('Y-m-d');
        $this->type = 'expense';
        $this->frequency = 'monthly';
        $this->due_day = now()->day;
        $this->is_active = true;
    }

    public function getStatusColor($recurring)
    {
        if (!$recurring->is_active) return 'gray';
        
        if ($recurring->next_execution && $recurring->next_execution->isPast()) {
            return 'red'; // Overdue
        }
        
        if ($recurring->next_execution && $recurring->next_execution->isToday()) {
            return 'yellow'; // Due today
        }

        return 'green'; // Active and scheduled
    }

    public function getStatusIcon($recurring)
    {
        if (!$recurring->is_active) return 'pause_circle';
        
        if ($recurring->next_execution && $recurring->next_execution->isPast()) {
            return 'error'; // Overdue
        }
        
        if ($recurring->next_execution && $recurring->next_execution->isToday()) {
            return 'schedule'; // Due today
        }

        return 'check_circle'; // Active and scheduled
    }

    public function getStatusText($recurring)
    {
        if (!$recurring->is_active) return 'Inativa';
        
        if ($recurring->next_execution && $recurring->next_execution->isPast()) {
            return 'Atrasada';
        }
        
        if ($recurring->next_execution && $recurring->next_execution->isToday()) {
            return 'Vence Hoje';
        }

        return 'Ativa';
    }

    public function getDueTransactionsCount()
    {
        return RecurringTransaction::dueForExecution()->count();
    }
    public function render()
    {
           // Process recurring transactions to add status properties
        if ($this->recurringTransactions) {
            foreach ($this->recurringTransactions as $recurring) {
                $recurring->status_color = $this->getStatusColor($recurring);
                $recurring->status_icon = $this->getStatusIcon($recurring);
                $recurring->status_text = $this->getStatusText($recurring);
            }
        }
        return view('livewire.recurring-transaction-manager', [
            'dueCount' => $this->getDueTransactionsCount()
        ])->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle
        ]);
    }
}
