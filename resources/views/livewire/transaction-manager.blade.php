{{-- resources/views/livewire/transaction-manager.blade.php --}}
<div class="p-4 lg:p-6">
    <!-- Header with Stats -->
    <div class="mb-6">
        <div class="flex items-center justify-between mb-4">
            <div>
                <h2 class="text-2xl font-semibold text-gray-900 dark:text-zinc-100">Transações</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Gerencie suas receitas e despesas</p>
            </div>
            <button wire:click="openCreateForm"
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors text-sm font-medium">
                <span class="material-icons text-base">add</span>
                <span>Nova Transação</span>
            </button>
        </div>

        <!-- Quick Stats -->
        {{-- <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-100 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-zinc-400">Receitas</p>
                        <p class="text-lg font-semibold text-success-600 dark:text-success-400">
                            +MZN {{ number_format($stats['totalIncome'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-success-600 dark:text-success-400">trending_up</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-100 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-zinc-400">Despesas</p>
                        <p class="text-lg font-semibold text-danger-600 dark:text-danger-400">
                            -MZN {{ number_format($stats['totalExpenses'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-danger-100 dark:bg-danger-900/30 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-danger-600 dark:text-danger-400">trending_down</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-100 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-zinc-400">Saldo</p>
                        <p class="text-lg font-semibold {{ $stats['balance'] >= 0 ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                            {{ $stats['balance'] >= 0 ? '+' : '' }}MZN {{ number_format($stats['balance'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-primary-100 dark:bg-primary-900/30 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-primary-600 dark:text-primary-400">account_balance_wallet</span>
                    </div>
                </div>
            </div>

            <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-100 dark:border-zinc-700">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600 dark:text-zinc-400">Transações</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                            {{ $stats['transactionCount'] }}
                        </p>
                    </div>
                    <div class="w-10 h-10 bg-gray-100 dark:bg-zinc-700 rounded-lg flex items-center justify-center">
                        <span class="material-icons text-gray-600 dark:text-zinc-400">receipt_long</span>
                    </div>
                </div>
            </div>
        </div> --}}
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl p-4 border border-gray-100 dark:border-zinc-700 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-6 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Buscar</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm"
                    placeholder="Descrição ou localização...">
            </div>

            <!-- Type Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Tipo</label>
                <select wire:model.live="filterType"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
                    <option value="all">Todos</option>
                    <option value="income">Receitas</option>
                    <option value="expense">Despesas</option>
                </select>
            </div>

            <!-- Category Filter -->
            <!-- Category Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Categoria</label>
                <select wire:model.live="filterCategory"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
                    <option value="">Todas</option>
                    @foreach ($categories->where('type', 'expense') as $category)
                        <option value="{{ $category->id }}">{{ $category->name }} (Despesa)</option>
                    @endforeach
                    @foreach ($categories->where('type', 'income') as $category)
                        <option value="{{ $category->id }}">{{ $category->name }} (Receita)</option>
                    @endforeach
                </select>
            </div>

            <!-- Date Start -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Data Início</label>
                <input type="date" wire:model.live="filterDateStart"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
            </div>

            <!-- Date End -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Data Fim</label>
                <input type="date" wire:model.live="filterDateEnd"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex justify-end mt-4">
            <button wire:click="clearFilters"
                class="bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                Limpar Filtros
            </button>
        </div>
    </div>

    <!-- Transactions List -->
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700">
        <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                Lista de Transações ({{ $transactions ? count($transactions) : 0 }})
            </h3>
        </div>

        @if ($transactions && count($transactions) > 0)
            <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                @if ($transactions)
                    @foreach ($transactions as $transaction)
                        <div class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <!-- Category Icon (sem relacionamento) -->
                                    <div
                                        class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100 dark:bg-zinc-700">
                                        <span
                                            class="material-icons text-base text-gray-600 dark:text-zinc-400">receipt_long</span>
                                    </div>

                                    <!-- Transaction Info -->
                                    <div>
                                        <h4 class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                            {{ $transaction->description }}
                                        </h4>
                                        <div class="flex items-center space-x-3 mt-1">
                                            <span class="text-xs text-gray-500 dark:text-zinc-400">
                                                {{ $transaction->type === 'income' ? 'Receita' : 'Despesa' }}
                                            </span>
                                            @if ($transaction->location)
                                                <span class="text-xs text-gray-500 dark:text-zinc-400">
                                                    <span class="material-icons text-xs mr-1">place</span>
                                                    {{ $transaction->location }}
                                                </span>
                                            @endif
                                            <span class="text-xs text-gray-500 dark:text-zinc-400">
                                                {{ \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Amount and Actions -->
                                <div class="flex items-center space-x-4">
                                    <div class="text-right">
                                        <p
                                            class="text-sm font-medium {{ $transaction->type === 'income' ? 'text-success-600 dark:text-success-400' : 'text-danger-600 dark:text-danger-400' }}">
                                            {{ $transaction->type === 'income' ? '+' : '-' }}MZN
                                            {{ number_format($transaction->amount, 2, ',', '.') }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                                            {{ \Carbon\Carbon::parse($transaction->transaction_date)->diffForHumans() }}
                                        </p>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex space-x-2">
                                        <button wire:click="openEditForm({{ $transaction->id }})"
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-zinc-300 transition-colors">
                                            <span class="material-icons text-sm">edit</span>
                                        </button>
                                        <button wire:click="delete({{ $transaction->id }})"
                                            wire:confirm="Tem certeza que deseja excluir esta transação?"
                                            class="p-2 text-gray-400 hover:text-danger-600 dark:hover:text-danger-400 transition-colors">
                                            <span class="material-icons text-sm">delete</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Sem paginação por enquanto -->
            @if (false)
                <div class="p-4 lg:p-6 border-t border-gray-200 dark:border-zinc-700">
                    <!-- Paginação aqui -->
                </div>
            @endif
        @else
            <div class="p-12 text-center">
                <span class="material-icons text-4xl text-gray-400 dark:text-zinc-500 mb-4">receipt_long</span>
                <p class="text-gray-500 dark:text-zinc-400">Nenhuma transação encontrada</p>
                <p class="text-sm text-gray-400 dark:text-zinc-500 mt-1">
                    @if ($search || $filterType !== 'all' || $filterCategory)
                        Tente ajustar os filtros ou
                    @endif
                    clique em "Nova Transação" para começar
                </p>
            </div>
        @endif
    </div>

    <!-- Modal Form -->
    @if ($showForm)
        <div class="fixed inset-0 z-50 bg-black/30 dark:bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full">
            <div
                class="relative top-10 mx-auto p-6 border w-96 shadow-2xl rounded-xl bg-white/95 dark:bg-zinc-800/95 backdrop-blur-md border-gray-200/50 dark:border-zinc-700/50">
                <div class="mt-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 text-center mb-6">
                        {{ $editingTransaction ? 'Editar Transação' : 'Nova Transação' }}
                    </h3>

                    <form wire:submit="{{ $editingTransaction ? 'update' : 'store' }}" class="space-y-4">
                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Tipo</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label
                                    class="flex items-center p-3 border border-gray-200 dark:border-zinc-600 rounded-lg cursor-pointer transition-all hover:shadow-sm
                                             {{ $type === 'expense' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                    <input type="radio" wire:model.live="type" value="expense" class="sr-only">
                                    <span class="material-icons text-red-500 mr-2 text-base">remove_circle</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Despesa</span>
                                </label>
                                <label
                                    class="flex items-center p-3 border border-gray-200 dark:border-zinc-600 rounded-lg cursor-pointer transition-all hover:shadow-sm
                                             {{ $type === 'income' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                    <input type="radio" wire:model.live="type" value="income" class="sr-only">
                                    <span class="material-icons text-green-500 mr-2 text-base">add_circle</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Receita</span>
                                </label>
                            </div>
                            @error('type')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Descrição -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Descrição</label>
                            <input type="text" wire:model="description"
                                class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 transition-colors text-sm"
                                placeholder="Ex: Compra no supermercado">
                            @error('description')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Valor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Valor
                                (MZN)</label>
                            <input type="number" step="0.01" wire:model="amount"
                                class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 transition-colors text-sm"
                                placeholder="0,00">
                            @error('amount')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Categoria -->
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Categoria</label>
                            {{-- <label
                                class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Categoria</label> --}}
                            <select wire:model="category_id"
                                class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 transition-colors text-sm">
                                <option value="">Selecione uma categoria</option>
                                @foreach ($categories->where('type', 'income') as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} (Receita)</option>
                                @endforeach

                                @foreach ($categories->where('type', 'expense') as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }} (Despesa)</option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Data -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Data</label>
                            <input type="date" wire:model="transaction_date"
                                class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 transition-colors text-sm">
                            @error('transaction_date')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Localização (Opcional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Localização
                                (Opcional)</label>
                            <input type="text" wire:model="location"
                                class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 transition-colors text-sm"
                                placeholder="Ex: Shoprite, Maputo">
                            @error('location')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" wire:click="resetForm"
                                class="px-4 py-2 bg-gray-200 dark:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg hover:bg-gray-300 dark:hover:bg-zinc-500 transition-colors text-sm font-medium">
                                Cancelar
                            </button>
                            <button type="submit"
                                class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center space-x-2 text-sm font-medium shadow-md hover:shadow-lg">
                                <span class="material-icons text-base">save</span>
                                <span>{{ $editingTransaction ? 'Atualizar' : 'Salvar' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
