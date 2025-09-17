<div>
    <!-- Header -->
    <header class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ $pageTitle }}</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                    Configure transações que se repetem automaticamente, como salário, aluguel e contas mensais.
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($dueCount > 0)
                    <button wire:click="executeAllDue" 
                        wire:confirm="Executar {{ $dueCount }} transação(ões) pendente(s)?"
                        class="inline-flex items-center px-3 py-2 bg-orange-600 hover:bg-orange-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <span class="material-icons mr-2 text-sm">play_arrow</span>
                        Executar Pendentes ({{ $dueCount }})
                    </button>
                @endif
                <button wire:click="showCreateForm" 
                    class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <span class="material-icons mr-2 text-lg">add</span>
                    Nova Recorrente
                </button>
            </div>
        </div>
    </header>

    <!-- Filters -->
    <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-700 bg-gray-50 dark:bg-zinc-800/50">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
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

            <!-- Frequency Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Frequência</label>
                <select wire:model.live="filterFrequency"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
                    <option value="all">Todas</option>
                    @foreach($frequencies as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Status Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Status</label>
                <select wire:model.live="filterStatus"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
                    <option value="all">Todos</option>
                    <option value="active">Ativas</option>
                    <option value="inactive">Inativas</option>
                    <option value="due">Vencidas</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Form Modal -->
    @if($showForm)
    <div class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4">
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="p-6 border-b border-gray-200 dark:border-zinc-700">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    {{ $editingTransaction ? 'Editar Transação Recorrente' : 'Nova Transação Recorrente' }}
                </h3>
            </div>
            
            <form wire:submit="save" class="p-6 space-y-6">
                <!-- Descrição -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Descrição *</label>
                    <input type="text" wire:model="description" 
                        class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm"
                        placeholder="Ex: Salário, Aluguel, Conta de Luz...">
                    @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Valor e Tipo -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Valor (MZN) *</label>
                        <input type="number" step="0.01" min="0.01" wire:model="amount"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm"
                            placeholder="0,00">
                        @error('amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Tipo *</label>
                        <select wire:model="type"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                            <option value="expense">Despesa</option>
                            <option value="income">Receita</option>
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Categoria -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Categoria *</label>
                    <select wire:model="category_id"
                        class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                        <option value="">Selecione uma categoria</option>
                        @foreach($categories->where('type', 'expense') as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} (Despesa)</option>
                        @endforeach
                        @foreach($categories->where('type', 'income') as $category)
                            <option value="{{ $category->id }}">{{ $category->name }} (Receita)</option>
                        @endforeach
                    </select>
                    @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Frequência e Dia de Vencimento -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Frequência *</label>
                        <select wire:model.live="frequency"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                            @foreach($frequencies as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('frequency') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    @if(in_array($frequency, ['weekly', 'monthly', 'bimonthly', 'quarterly', 'yearly']))
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                                @if($frequency === 'weekly')
                                    Dia da Semana (1=Segunda, 7=Domingo)
                                @else
                                    Dia do Mês (1-31)
                                @endif
                            </label>
                            <input type="number" 
                                min="{{ $frequency === 'weekly' ? '1' : '1' }}" 
                                max="{{ $frequency === 'weekly' ? '7' : '31' }}" 
                                wire:model="due_day"
                                class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                            @error('due_day') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        </div>
                    @endif
                </div>

                <!-- Datas -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Data de Início *</label>
                        <input type="date" wire:model="start_date"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                        @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                            Data de Fim 
                            <span class="text-gray-500 text-xs">(opcional - deixe vazio para nunca parar)</span>
                        </label>
                        <input type="date" wire:model="end_date"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                        @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Status -->
                <div class="flex items-center">
                    <input type="checkbox" wire:model="is_active" id="is_active" 
                        class="w-4 h-4 text-primary-600 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded focus:ring-primary-500 dark:focus:ring-primary-600 focus:ring-2">
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-zinc-300">Transação Ativa</label>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <button type="button" wire:click="cancelForm"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-600 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        {{ $editingTransaction ? 'Atualizar' : 'Criar' }} Transação
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Recurring Transactions List -->
    <main class="p-4 lg:p-6">
        @if($recurringTransactions && count($recurringTransactions) > 0)
            <div class="space-y-4">
                @foreach($recurringTransactions as $recurring)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-6">
                        <div class="flex items-start justify-between">
                            <!-- Left side - Main info -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-3">
                                    <!-- Category Icon -->
                                    <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-{{ $recurring->type === 'income' ? 'green' : 'red' }}-100 dark:bg-{{ $recurring->type === 'income' ? 'green' : 'red' }}-900/30">
                                        <span class="material-icons text-{{ $recurring->type === 'income' ? 'green' : 'red' }}-600 dark:text-{{ $recurring->type === 'income' ? 'green' : 'red' }}-400 text-lg">
                                            {{ $recurring->type === 'income' ? 'trending_up' : 'trending_down' }}
                                        </span>
                                    </div>
                                    
                                    <!-- Transaction Info -->
                                    <div class="flex-1">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                                            {{ $recurring->description }}
                                        </h3>
                                        <div class="flex items-center space-x-4 mt-1">
                                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                                {{ $recurring->category->name }}
                                            </span>
                                            <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                                {{ $recurring->status_color === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                                {{ $recurring->status_color === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                                {{ $recurring->status_color === 'red' ? 'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400' : '' }}
                                                {{ $recurring->status_color === 'gray' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' : '' }}">
                                                <span class="material-icons text-xs mr-1">{{ $recurring->status_icon }}</span>
                                                {{ $recurring->status_text }}
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Details Grid -->
                                <div class="grid grid-cols-2 md:grid-cols-4 gap-4 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400 block">Valor</span>
                                        <span class="font-medium text-gray-900 dark:text-zinc-100">{{ $recurring->formatted_amount }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400 block">Frequência</span>
                                        <span class="font-medium text-gray-900 dark:text-zinc-100">{{ $frequencies[$recurring->frequency] }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400 block">Próxima Execução</span>
                                        <span class="font-medium text-gray-900 dark:text-zinc-100">
                                            {{ $recurring->next_execution ? $recurring->next_execution->format('d/m/Y') : 'N/A' }}
                                        </span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-zinc-400 block">Última Execução</span>
                                        <span class="font-medium text-gray-900 dark:text-zinc-100">
                                            {{ $recurring->last_execution ? $recurring->last_execution->format('d/m/Y') : 'Nunca' }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <!-- Right side - Actions -->
                            <div class="flex items-center space-x-2">
                                <!-- Execute Now Button -->
                                @if($recurring->is_active && $recurring->next_execution && $recurring->next_execution->isPast())
                                    <button wire:click="executeNow({{ $recurring->id }})"
                                        wire:confirm="Executar esta transação agora?"
                                        class="p-2 bg-orange-100 hover:bg-orange-200 dark:bg-orange-900/30 dark:hover:bg-orange-900/50 text-orange-600 dark:text-orange-400 rounded-lg transition-colors"
                                        title="Executar Agora">
                                        <span class="material-icons text-sm">play_arrow</span>
                                    </button>
                                @endif

                                <!-- Actions Dropdown -->
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700">
                                        <span class="material-icons text-gray-500 dark:text-zinc-400">more_vert</span>
                                    </button>
                                    <div x-show="open" @click.away="open = false" 
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 z-10"
                                         style="display: none;">
                                        <button wire:click="showEditForm({{ $recurring->id }})" @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center">
                                            <span class="material-icons text-sm mr-2">edit</span>
                                            Editar
                                        </button>
                                        @if($recurring->is_active)
                                            <button wire:click="executeNow({{ $recurring->id }})" @click="open = false"
                                                wire:confirm="Executar esta transação agora?"
                                                class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center">
                                                <span class="material-icons text-sm mr-2">play_arrow</span>
                                                Executar Agora
                                            </button>
                                        @endif
                                        <button wire:click="toggleStatus({{ $recurring->id }})" @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center">
                                            <span class="material-icons text-sm mr-2">
                                                {{ $recurring->is_active ? 'pause' : 'play_arrow' }}
                                            </span>
                                            {{ $recurring->is_active ? 'Desativar' : 'Ativar' }}
                                        </button>
                                        <button wire:click="delete({{ $recurring->id }})" @click="open = false"
                                            wire:confirm="Tem certeza que deseja excluir esta transação recorrente? Isso não afetará as transações já geradas."
                                            class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center rounded-b-lg">
                                            <span class="material-icons text-sm mr-2">delete</span>
                                            Excluir
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Additional Info -->
                        @if($recurring->due_day || $recurring->end_date)
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700 flex items-center justify-between text-xs text-gray-500 dark:text-zinc-400">
                                <div class="flex items-center space-x-4">
                                    @if($recurring->due_day)
                                        <span class="flex items-center">
                                            <span class="material-icons text-xs mr-1">schedule</span>
                                            @if($recurring->frequency === 'weekly')
                                                {{ ['', 'Segunda', 'Terça', 'Quarta', 'Quinta', 'Sexta', 'Sábado', 'Domingo'][$recurring->due_day] ?? 'Dia ' . $recurring->due_day }}
                                            @else
                                                Dia {{ $recurring->due_day }}
                                            @endif
                                        </span>
                                    @endif
                                    @if($recurring->end_date)
                                        <span class="flex items-center">
                                            <span class="material-icons text-xs mr-1">event</span>
                                            Termina em {{ $recurring->end_date->format('d/m/Y') }}
                                        </span>
                                    @endif
                                </div>
                                <div class="text-right">
                                    <span class="flex items-center">
                                        <span class="material-icons text-xs mr-1">history</span>
                                        {{ $recurring->transactions()->count() }} execução(ões)
                                    </span>
                                </div>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>

        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                    <span class="material-icons text-2xl text-gray-400 dark:text-zinc-500">autorenew</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">Nenhuma transação recorrente encontrada</h3>
                <p class="text-gray-500 dark:text-zinc-400 mb-6">
                    @if($filterType !== 'all' || $filterFrequency !== 'all' || $filterStatus !== 'all')
                        Tente ajustar os filtros para ver mais resultados.
                    @else
                        Configure transações automáticas como salário, aluguel, contas e outros pagamentos regulares.
                    @endif
                </p>
                @if($filterType === 'all' && $filterFrequency === 'all' && $filterStatus === 'all')
                    <button wire:click="showCreateForm" 
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <span class="material-icons mr-2 text-lg">add</span>
                        Criar Primeira Recorrente
                    </button>
                @endif
            </div>
        @endif
    </main>
</div>