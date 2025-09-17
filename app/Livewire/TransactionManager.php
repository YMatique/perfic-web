<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Transaction;
use App\Traits\WithToast;
use Livewire\Attributes\Rule;
use Livewire\Component;
use Livewire\WithPagination;

class TransactionManager extends Component
{
    use WithToast, WithPagination;

 public $title = 'Transações';
    public $pageTitle = 'Gestão de Transações';

    // Form properties
    #[Rule('required|string|max:255')]
    public $description = '';

    #[Rule('required|numeric|min:0.01')]
    public $amount = '';

    #[Rule('required|in:income,expense')]
    public $type = 'expense';

    #[Rule('required|exists:categories,id')]
    public $category_id = '';

    #[Rule('required|date')]
    public $transaction_date = '';

    #[Rule('nullable|string|max:255')]
    public $location = '';

    // Component state
    public $transactions;
    public $categories;
    public $showForm = false;
    public $editingTransaction = null;
    
    // Filters
    public $filterType = 'all';
    public $filterCategory = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $search = '';

    public function mount()
    {
        $this->loadCategories();
        $this->loadTransactions();
        $this->transaction_date = now()->format('Y-m-d');
        $this->filterDateStart = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateEnd = now()->endOfMonth()->format('Y-m-d');
    }

    public function loadTransactions()
    {
        // $query = Transaction::query()
        //     ->orderBy('transaction_date', 'desc')
        //     ->orderBy('created_at', 'desc');
         $query = Transaction::query()// Desabilita TODOS os global scopes
            ->orderBy('transaction_date', 'desc')
            ->orderBy('created_at', 'desc');

             if (auth()->check()) {
            $query->where('tenant_id', auth()->id());
        }

        // Apply filters
        if ($this->filterType !== 'all') {
            $query->where('type', $this->filterType);
        }

        if ($this->filterCategory) {
            $query->where('category_id', $this->filterCategory);
        }

        if ($this->filterDateStart && $this->filterDateEnd) {
            $query->whereBetween('transaction_date', [
                $this->filterDateStart,
                $this->filterDateEnd
            ]);
        }

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('description', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%');
            });
        }

        $this->transactions = $query->get();
    }

    public function loadCategories()
    {
        $this->categories = Category::active()
            ->ordered()
            ->get();
            // ->groupBy('type');
    }

    public function openCreateForm()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEditForm(Transaction $transaction)
    {
        $this->resetForm();
        $this->editingTransaction = $transaction;
        $this->description = $transaction->description;
        $this->amount = $transaction->amount;
        $this->type = $transaction->type;
        $this->category_id = $transaction->category_id;
        $this->transaction_date = $transaction->transaction_date->format('Y-m-d');
        $this->location = $transaction->location;
        $this->showForm = true;
    }

    public function store()
    {
        $this->validate();

        try {
            Transaction::create([
                'tenant_id' => auth()->id(),
                'description' => $this->description,
                'amount' => $this->amount,
                'type' => $this->type,
                'category_id' => $this->category_id,
                'transaction_date' => $this->transaction_date,
                'location' => $this->location,
            ]);

            $this->toastSuccess('Transação criada!', 'A transação foi registrada com sucesso.');
            $this->resetForm();
            $this->loadTransactions();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao criar transação: ' . $e->getMessage());
        }
    }

    public function update()
    {
        $this->validate();

        try {
            $this->editingTransaction->update([
                'description' => $this->description,
                'amount' => $this->amount,
                'type' => $this->type,
                'category_id' => $this->category_id,
                'transaction_date' => $this->transaction_date,
                'location' => $this->location,
            ]);

            $this->toastSuccess('Transação atualizada!', 'A transação foi atualizada com sucesso.');
            $this->resetForm();
            $this->loadTransactions();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao atualizar transação: ' . $e->getMessage());
        }
    }

    public function delete(Transaction $transaction)
    {
        try {
            $transaction->delete();
            $this->toastSuccess('Transação excluída!', 'A transação foi excluída com sucesso.');
            $this->loadTransactions();
            
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao excluir transação: ' . $e->getMessage());
        }
    }

    public function clearFilters()
    {
        $this->filterType = 'all';
        $this->filterCategory = '';
        $this->search = '';
        $this->filterDateStart = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateEnd = now()->endOfMonth()->format('Y-m-d');
        $this->loadTransactions();
    }

    // Filter update methods
    public function updatedFilterType()
    {
        $this->loadTransactions();
    }

    public function updatedFilterCategory()
    {
        $this->loadTransactions();
    }

    public function updatedSearch()
    {
        $this->loadTransactions();
    }

    public function updatedFilterDateStart()
    {
        $this->loadTransactions();
    }

    public function updatedFilterDateEnd()
    {
        $this->loadTransactions();
    }

    public function resetForm()
    {
        $this->description = '';
        $this->amount = '';
        $this->type = 'expense';
        $this->category_id = '';
        $this->transaction_date = now()->format('Y-m-d');
        $this->location = '';
        $this->editingTransaction = null;
        $this->showForm = false;
        $this->resetValidation();
    }

   public function loadStats()
{
    $dateRange = [
        $this->filterDateStart ?: now()->startOfMonth(),
        $this->filterDateEnd ?: now()->endOfMonth()
    ];

    $baseQuery = Transaction::query()
        ->whereBetween('transaction_date', $dateRange);

    if (auth()->check()) {
        $baseQuery->where('tenant_id', auth()->id());
    }

    $totalIncome = (clone $baseQuery)->where('type', 'income')->sum('amount');
    $totalExpenses = (clone $baseQuery)->where('type', 'expense')->sum('amount');
    $balance = $totalIncome - $totalExpenses;
    $transactionCount = (clone $baseQuery)->count();

    return [
        'totalIncome' => $totalIncome,
        'totalExpenses' => $totalExpenses,
        'balance' => $balance,
        'transactionCount' => $transactionCount,
    ];
}
    public function render()
    {

         // Teste básico - sem stats por enquanto
    // try {
    //     $testQuery = Transaction::query()->limit(1)->get();
    //     dd("Funcionou!", $testQuery);
    // } catch (\Exception $e) {
    //     dd("Erro:", $e->getMessage(), $e->getTraceAsString());
    // }
    // Teste só as stats
    // try {
    //     $stats = $this->getStatsProperty();
    //     dd("Stats funcionaram!", $stats);
    // } catch (\Exception $e) {
    //     dd("Erro nas stats:", $e->getMessage(), $e->getTraceAsString());
    // }
        return view('livewire.transaction-manager',  [
            //  'stats' => $this->loadStats() 
        ])->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle
        ]);
    }
}
