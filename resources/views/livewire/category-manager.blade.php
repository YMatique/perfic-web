<div class="p-4 lg:p-6">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-semibold text-gray-900 dark:text-zinc-100">Categorias</h2>
            <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Gerencie suas categorias de receitas e despesas</p>
        </div>
        <button wire:click="openCreateForm" 
                class="bg-primary-600 hover:bg-primary-700 text-white px-4 py-2 rounded-lg flex items-center space-x-2 transition-colors">
            <span class="material-icons text-sm">add</span>
            <span>Nova Categoria</span>
        </button>
    </div>

    <!-- Filters -->
    <div class="mb-6">
        <div class="flex space-x-2">
            <button wire:click="$set('filterType', 'all')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $filterType === 'all' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                Todas
            </button>
            <button wire:click="$set('filterType', 'income')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $filterType === 'income' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                Receitas
            </button>
            <button wire:click="$set('filterType', 'expense')" 
                    class="px-4 py-2 rounded-lg text-sm font-medium transition-colors
                           {{ $filterType === 'expense' ? 'bg-primary-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}">
                Despesas
            </button>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
        @forelse ($categories as $category)
            <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 border border-gray-100 dark:border-zinc-700 hover:shadow-md transition-shadow">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                             style="background-color: {{ $category->color }}20; color: {{ $category->color }}">
                            <span class="material-icons text-lg">{{ $category->icon }}</span>
                        </div>
                        <div>
                            <h3 class="font-medium text-gray-900 dark:text-zinc-100">{{ $category->name }}</h3>
                            <p class="text-xs text-gray-500 dark:text-zinc-400">
                                {{ $category->type === 'income' ? 'Receita' : 'Despesa' }}
                            </p>
                        </div>
                    </div>
                    
                    <!-- Status Badge -->
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $category->is_active 
                                   ? 'bg-success-100 dark:bg-success-900/30 text-success-700 dark:text-success-300' 
                                   : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300' }}">
                        {{ $category->is_active ? 'Ativa' : 'Inativa' }}
                    </span>
                </div>

                <!-- Stats -->
                <div class="mb-4">
                    <p class="text-sm text-gray-600 dark:text-zinc-400">
                        {{ $category->transactions()->count() }} transações
                    </p>
                </div>

                <!-- Actions -->
                <div class="flex space-x-2">
                    <button wire:click="openEditForm({{ $category->id }})" 
                            class="flex-1 bg-gray-100 dark:bg-zinc-700 hover:bg-gray-200 dark:hover:bg-zinc-600 text-gray-700 dark:text-zinc-300 px-3 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center space-x-1">
                        <span class="material-icons text-sm">edit</span>
                        <span>Editar</span>
                    </button>
                    
                    <button wire:click="toggleStatus({{ $category->id }})" 
                            class="bg-warning-100 dark:bg-warning-900/30 hover:bg-warning-200 dark:hover:bg-warning-900/50 text-warning-700 dark:text-warning-300 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                        <span class="material-icons text-sm">power_settings_new</span>
                    </button>
                    
                    @if($category->transactions()->count() === 0)
                        <button wire:click="delete({{ $category->id }})" 
                                wire:confirm="Tem certeza que deseja excluir esta categoria?"
                                class="bg-danger-100 dark:bg-danger-900/30 hover:bg-danger-200 dark:hover:bg-danger-900/50 text-danger-700 dark:text-danger-300 px-3 py-2 rounded-lg text-sm font-medium transition-colors">
                            <span class="material-icons text-sm">delete</span>
                        </button>
                    @endif
                </div>
            </div>
        @empty
            <div class="col-span-full text-center py-12">
                <span class="material-icons text-6xl text-gray-400 dark:text-zinc-500 mb-4">folder_off</span>
                <p class="text-gray-500 dark:text-zinc-400">Nenhuma categoria encontrada</p>
                <p class="text-sm text-gray-400 dark:text-zinc-500 mt-1">Clique em "Nova Categoria" para começar</p>
            </div>
        @endforelse
    </div>

    <!-- Modal Form -->
    @if($showForm)
        <div class="fixed inset-0 z-50 bg-black/30 dark:bg-black/50 backdrop-blur-sm overflow-y-auto h-full w-full">
            <div class="relative top-20 mx-auto p-6 border w-96 shadow-2xl rounded-xl bg-white/95 dark:bg-zinc-800/95 backdrop-blur-md border-gray-200/50 dark:border-zinc-700/50">
                <div class="mt-3">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 text-center mb-6">
                        {{ $editingCategory ? 'Editar Categoria' : 'Nova Categoria' }}
                    </h3>
                    
                    <form wire:submit="{{ $editingCategory ? 'update' : 'store' }}" class="space-y-4">
                        <!-- Nome -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Nome</label>
                            <input type="text" 
                                   wire:model="name" 
                                   class="w-full border border-gray-200 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:ring-2 focus:ring-primary-500/50 focus:border-primary-500 px-3 py-2.5 transition-colors" 
                                   placeholder="Ex: Alimentação">
                            @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tipo -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Tipo</label>
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center p-3 border border-gray-200 dark:border-zinc-600 rounded-lg cursor-pointer transition-all hover:shadow-sm
                                             {{ $type === 'expense' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                    <input type="radio" wire:model.live="type" value="expense" class="sr-only">
                                    <span class="material-icons text-red-500 mr-2 text-xl">remove_circle</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Despesa</span>
                                </label>
                                <label class="flex items-center p-3 border border-gray-200 dark:border-zinc-600 rounded-lg cursor-pointer transition-all hover:shadow-sm
                                             {{ $type === 'income' ? 'border-primary-500 bg-primary-50 dark:bg-primary-900/20 shadow-sm' : 'hover:bg-gray-50 dark:hover:bg-zinc-700' }}">
                                    <input type="radio" wire:model.live="type" value="income" class="sr-only">
                                    <span class="material-icons text-green-500 mr-2 text-xl">add_circle</span>
                                    <span class="text-sm font-medium text-gray-700 dark:text-zinc-300">Receita</span>
                                </label>
                            </div>
                            @error('type') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Ícone -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Ícone</label>
                            <div class="grid grid-cols-6 gap-2 max-h-32 overflow-y-auto p-3 border border-gray-200 dark:border-zinc-600 rounded-lg bg-gray-50 dark:bg-zinc-900">
                                @foreach($availableIcons as $iconName => $iconLabel)
                                    <button type="button" 
                                            wire:click="$set('icon', '{{ $iconName }}')"
                                            class="p-2.5 rounded-lg transition-all hover:scale-105
                                                   {{ $icon === $iconName ? 'bg-primary-500 text-white shadow-md' : 'bg-white dark:bg-zinc-800 text-gray-700 dark:text-zinc-300 hover:bg-gray-100 dark:hover:bg-zinc-700 border border-gray-200 dark:border-zinc-600' }}"
                                            title="{{ $iconLabel }}">
                                        <span class="material-icons text-lg">{{ $iconName }}</span>
                                    </button>
                                @endforeach
                            </div>
                            @error('icon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Cor -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Cor</label>
                            <div class="grid grid-cols-9 gap-2">
                                @foreach($availableColors as $colorValue => $colorName)
                                    <button type="button" 
                                            wire:click="$set('color', '{{ $colorValue }}')"
                                            class="w-8 h-8 rounded-lg border-2 transition-all
                                                   {{ $color === $colorValue ? 'border-gray-800 dark:border-white scale-110' : 'border-gray-300 dark:border-zinc-600' }}"
                                            style="background-color: {{ $colorValue }}"
                                            title="{{ $colorName }}">
                                    </button>
                                @endforeach
                            </div>
                            @error('color') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Preview -->
                        <div class="bg-gray-50 dark:bg-zinc-700 p-3 rounded-lg">
                            <p class="text-xs text-gray-600 dark:text-zinc-400 mb-2">Pré-visualização:</p>
                            <div class="flex items-center space-x-3">
                                <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                     style="background-color: {{ $color }}20; color: {{ $color }}">
                                    <span class="material-icons text-lg">{{ $icon }}</span>
                                </div>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-zinc-100">{{ $name ?: 'Nome da categoria' }}</p>
                                    <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $type === 'income' ? 'Receita' : 'Despesa' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex justify-end space-x-3 pt-4">
                            <button type="button" 
                                    wire:click="resetForm"
                                    class="px-5 py-2.5 bg-gray-200 dark:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg hover:bg-gray-300 dark:hover:bg-zinc-500 transition-colors font-medium">
                                Cancelar
                            </button>
                            <button type="submit" 
                                    class="px-5 py-2.5 bg-primary-600 text-white rounded-lg hover:bg-primary-700 transition-colors flex items-center space-x-2 font-medium shadow-md hover:shadow-lg">
                                <span class="material-icons text-sm">save</span>
                                <span>{{ $editingCategory ? 'Atualizar' : 'Salvar' }}</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>