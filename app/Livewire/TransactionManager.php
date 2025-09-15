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
    public $categories;
    public $showForm = false;
    public $editingTransaction = null;

    // Filters
    public $filterType = 'all'; // all, income, expense
    public $filterCategory = '';
    public $filterDateStart = '';
    public $filterDateEnd = '';
    public $search = '';

    // Pagination
    // protected $paginationView = 'livewire.custom-pagination';

    public function mount()
    {
        $this->loadCategories();
        $this->transaction_date = now()->format('Y-m-d');
        $this->filterDateStart = now()->startOfMonth()->format('Y-m-d');
        $this->filterDateEnd = now()->endOfMonth()->format('Y-m-d');
    }

    public function loadCategories()
    {
        $this->categories = Category::active()
            ->ordered()
            ->get()
            ->groupBy('type');
    }

    public function getTransactionsProperty()
    {
        $query = Transaction::with('category')
            ->recent();

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

        return $query->paginate(15);
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
        } catch (\Exception $e) {
            $this->toastError('Erro!', 'Erro ao atualizar transação: ' . $e->getMessage());
        }
    }

    public function delete(Transaction $transaction)
    {
        try {
            $transaction->delete();
            $this->toastSuccess('Transação excluída!', 'A transação foi excluída com sucesso.');
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
        $this->resetPage();
    }

    public function updatedFilterType()
    {
        $this->resetPage();
    }

    public function updatedFilterCategory()
    {
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedFilterDateStart()
    {
        $this->resetPage();
    }

    public function updatedFilterDateEnd()
    {
        $this->resetPage();
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

    // Quick stats for dashboard
    public function getStatsProperty()
    {
        $dateRange = [
            $this->filterDateStart ?: now()->startOfMonth(),
            $this->filterDateEnd ?: now()->endOfMonth()
        ];

        $totalIncome = Transaction::where('type', 'income')
            ->whereBetween('transaction_date', $dateRange)
            ->sum('amount');

        $totalExpenses = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', $dateRange)
            ->sum('amount');

        $balance = $totalIncome - $totalExpenses;

        $transactionCount = Transaction::whereBetween('transaction_date', $dateRange)
            ->count();

        return [
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'balance' => $balance,
            'transactionCount' => $transactionCount,
        ];
    }

    public function render()
    {
        // dd($this->getTransactionsProperty());
        return view('livewire.transaction-manager',  [
            'transactions' => $this->getTransactionsProperty(),
            'stats' => $this->getStatsProperty()
        ])->layout('components.layouts.perfic-layout', [
            'title' => $this->title,
            'pageTitle' => $this->pageTitle
        ]);
    }
}
