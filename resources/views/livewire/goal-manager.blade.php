<div class="p-4 lg:p-6">
    <!-- Header -->
    <header class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-700">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100">{{ $pageTitle }}</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                    Defina e acompanhe suas metas financeiras para manter o controle dos seus gastos e poupanças.
                </p>
            </div>
            <button wire:click="showCreateForm" 
                class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons mr-2 text-lg">add</span>
                Nova Meta
            </button>
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
                    <option value="all">Todos os Tipos</option>
                    @foreach($goalTypes as $key => $label)
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
                    <option value="completed">Concluídas</option>
                    <option value="warning">Em Atenção</option>
                </select>
            </div>

            <!-- Period Filter -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-1">Período</label>
                <select wire:model.live="filterPeriod"
                    class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2 text-sm">
                    <option value="all">Todos os Períodos</option>
                    @foreach($periods as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
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
                    {{ $editingGoal ? 'Editar Meta' : 'Nova Meta' }}
                </h3>
            </div>
            
            <form wire:submit="save" class="p-6 space-y-6">
                <!-- Nome -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Nome da Meta *</label>
                    <input type="text" wire:model="name" 
                        class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm"
                        placeholder="Ex: Meta Alimentação Março">
                    @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                </div>

                <!-- Tipo e Categoria -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Tipo *</label>
                        <select wire:model.live="type"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                            @foreach($goalTypes as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('type') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">
                            Categoria 
                            @if(in_array($type, ['category_limit']))
                                <span class="text-red-500">*</span>
                            @else
                                <span class="text-gray-500 text-xs">(opcional)</span>
                            @endif
                        </label>
                        <select wire:model="category_id"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                            <option value="">Selecione uma categoria</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }} ({{ $category->type === 'income' ? 'Receita' : 'Despesa' }})</option>
                            @endforeach
                        </select>
                        @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                </div>

                <!-- Valor e Período -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Valor da Meta (MZN) *</label>
                        <input type="number" step="0.01" min="0.01" wire:model="target_amount"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm"
                            placeholder="0,00">
                        @error('target_amount') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Período *</label>
                        <select wire:model="period"
                            class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 text-sm">
                            @foreach($periods as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        @error('period') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
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
                            <span class="text-gray-500 text-xs">(opcional para períodos fixos)</span>
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
                    <label for="is_active" class="ml-2 text-sm text-gray-700 dark:text-zinc-300">Meta Ativa</label>
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-3 pt-4 border-t border-gray-200 dark:border-zinc-700">
                    <button type="button" wire:click="cancelForm"
                        class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-zinc-300 bg-white dark:bg-zinc-700 border border-gray-300 dark:border-zinc-600 rounded-lg hover:bg-gray-50 dark:hover:bg-zinc-600 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit"
                        class="px-4 py-2 text-sm font-medium text-white bg-primary-600 hover:bg-primary-700 rounded-lg transition-colors">
                        {{ $editingGoal ? 'Atualizar' : 'Criar' }} Meta
                    </button>
                </div>
            </form>
        </div>
    </div>
    @endif

    <!-- Goals List -->
    <main class="p-4 lg:p-6">
        @if($goals && count($goals) > 0)
            <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($goals as $goal)
                    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700 p-6">
                        <!-- Header -->
                        <div class="flex items-start justify-between mb-4">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-1">
                                    {{ $goal->name }}
                                </h3>
                                <div class="flex items-center space-x-2">
                                    <span class="inline-flex items-center px-2 py-1 text-xs font-medium rounded-full
                                        {{ $getGoalStatusColor($goal) === 'green' ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' : '' }}
                                        {{ $getGoalStatusColor($goal) === 'yellow' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' : '' }}
                                        {{ $getGoalStatusColor($goal) === 'blue' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400' : '' }}
                                        {{ $getGoalStatusColor($goal) === 'gray' ? 'bg-gray-100 text-gray-800 dark:bg-gray-900/30 dark:text-gray-400' : '' }}">
                                        <span class="material-icons text-xs mr-1">{{ $getGoalStatusIcon($goal) }}</span>
                                        {{ $getGoalStatusText($goal) }}
                                    </span>
                                    <span class="text-xs text-gray-500 dark:text-zinc-400">
                                        {{ $goalTypes[$goal->type] }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Actions Dropdown -->
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="p-1 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700">
                                    <span class="material-icons text-gray-500 dark:text-zinc-400">more_vert</span>
                                </button>
                                <div x-show="open" @click.away="open = false" 
                                     class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 z-10"
                                     style="display: none;">
                                    <button wire:click="showEditForm({{ $goal->id }})" @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center">
                                        <span class="material-icons text-sm mr-2">edit</span>
                                        Editar
                                    </button>
                                    <button wire:click="calculateProgress({{ $goal->id }})" @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center">
                                        <span class="material-icons text-sm mr-2">refresh</span>
                                        Recalcular
                                    </button>
                                    <button wire:click="toggleStatus({{ $goal->id }})" @click="open = false"
                                        class="w-full text-left px-4 py-2 text-sm text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 flex items-center">
                                        <span class="material-icons text-sm mr-2">
                                            {{ $goal->is_active ? 'pause' : 'play_arrow' }}
                                        </span>
                                        {{ $goal->is_active ? 'Desativar' : 'Ativar' }}
                                    </button>
                                    <button wire:click="delete({{ $goal->id }})" @click="open = false"
                                        wire:confirm="Tem certeza que deseja excluir esta meta?"
                                        class="w-full text-left px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center rounded-b-lg">
                                        <span class="material-icons text-sm mr-2">delete</span>
                                        Excluir
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Progress -->
                        <div class="mb-4">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">Progresso</span>
                                <span class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                    {{ number_format($goal->progress_percentage, 1) }}%
                                </span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-700 rounded-full h-3">
                                <div class="h-3 rounded-full transition-all duration-300
                                    {{ $getGoalStatusColor($goal) === 'green' ? 'bg-green-500' : '' }}
                                    {{ $getGoalStatusColor($goal) === 'yellow' ? 'bg-yellow-500' : '' }}
                                    {{ $getGoalStatusColor($goal) === 'blue' ? 'bg-blue-500' : '' }}
                                    {{ $getGoalStatusColor($goal) === 'gray' ? 'bg-gray-500' : '' }}"
                                    style="width: {{ min(100, $goal->progress_percentage) }}%">
                                </div>
                            </div>
                        </div>

                        <!-- Values -->
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-zinc-400">Atual:</span>
                                <span class="font-medium text-gray-900 dark:text-zinc-100">{{ $goal->formatted_current_progress }}</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-gray-600 dark:text-zinc-400">Meta:</span>
                                <span class="font-medium text-gray-900 dark:text-zinc-100">{{ $goal->formatted_target_amount }}</span>
                            </div>
                            @if($goal->remaining_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600 dark:text-zinc-400">Restante:</span>
                                    <span class="font-medium text-gray-900 dark:text-zinc-100">
                                        MZN {{ number_format($goal->remaining_amount, 2, ',', '.') }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- Category & Period -->
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-zinc-700">
                            <div class="flex items-center justify-between text-xs text-gray-500 dark:text-zinc-400">
                                <div class="flex items-center">
                                    @if($goal->category)
                                        <span class="material-icons text-sm mr-1">category</span>
                                        {{ $goal->category->name }}
                                    @else
                                        <span class="material-icons text-sm mr-1">all_inclusive</span>
                                        Todas as categorias
                                    @endif
                                </div>
                                <div class="flex items-center">
                                    <span class="material-icons text-sm mr-1">schedule</span>
                                    {{ $periods[$goal->period] }}
                                </div>
                            </div>
                            @if($goal->start_date && $goal->end_date)
                                <div class="mt-1 text-xs text-gray-500 dark:text-zinc-400">
                                    {{ $goal->start_date->format('d/m/Y') }} - {{ $goal->end_date->format('d/m/Y') }}
                                </div>
                            @elseif($goal->start_date)
                                <div class="mt-1 text-xs text-gray-500 dark:text-zinc-400">
                                    Desde {{ $goal->start_date->format('d/m/Y') }}
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination would go here if needed -->
            
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-zinc-700 rounded-full flex items-center justify-center">
                    <span class="material-icons text-2xl text-gray-400 dark:text-zinc-500">flag</span>
                </div>
                <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">Nenhuma meta encontrada</h3>
                <p class="text-gray-500 dark:text-zinc-400 mb-6">
                    @if($filterType !== 'all' || $filterStatus !== 'all' || $filterPeriod !== 'all')
                        Tente ajustar os filtros para ver mais resultados.
                    @else
                        Comece criando sua primeira meta financeira para manter o controle dos seus gastos.
                    @endif
                </p>
                @if($filterType === 'all' && $filterStatus === 'all' && $filterPeriod === 'all')
                    <button wire:click="showCreateForm" 
                        class="inline-flex items-center px-4 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-medium rounded-lg transition-colors">
                        <span class="material-icons mr-2 text-lg">add</span>
                        Criar Primeira Meta
                    </button>
                @endif
            </div>
        @endif
    </main>
</div>