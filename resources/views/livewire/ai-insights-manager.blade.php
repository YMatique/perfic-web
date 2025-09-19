<div class="p-4 lg:p-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-zinc-100">
                    Insights de IA
                </h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                    Análise inteligente dos seus hábitos financeiros
                </p>
            </div>
            
            <div class="mt-4 lg:mt-0">
                <button 
                    wire:click="generateNewInsights"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors disabled:opacity-50"
                >
                    <span class="material-icons text-sm mr-2" wire:loading.remove>psychology</span>
                    <span wire:loading.remove>Gerar Novos Insights</span>
                    <svg wire:loading class="animate-spin h-5 w-5 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span wire:loading>Analisando...</span>
                </button>
            </div>
        </div>
    </div>

    {{-- Financial Score & User Profile --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Financial Score Card --}}
        @if($financialScore)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-4">
                    Score Financeiro
                </h3>
                
                <div class="flex items-center justify-between mb-4">
                    <div class="flex-1">
                        <div class="text-5xl font-bold {{ $financialScore->score >= 80 ? 'text-green-600' : ($financialScore->score >= 60 ? 'text-blue-600' : ($financialScore->score >= 40 ? 'text-yellow-600' : 'text-red-600')) }}">
                            {{ $financialScore->score }}
                        </div>
                        <p class="text-sm text-gray-600 dark:text-zinc-400 mt-2">
                            {{ $this->getScoreMessage($financialScore->score) }}
                        </p>
                    </div>
                    
                    <div class="w-32 h-32">
                        <svg class="transform -rotate-90" width="128" height="128">
                            <circle cx="64" cy="64" r="54" stroke="currentColor" stroke-width="10" fill="none" class="text-gray-200 dark:text-gray-700" />
                            <circle cx="64" cy="64" r="54" stroke="currentColor" stroke-width="10" fill="none" 
                                class="{{ $financialScore->score >= 80 ? 'text-green-600' : ($financialScore->score >= 60 ? 'text-blue-600' : ($financialScore->score >= 40 ? 'text-yellow-600' : 'text-red-600')) }}"
                                stroke-dasharray="{{ 2 * 3.14159 * 54 }}"
                                stroke-dashoffset="{{ 2 * 3.14159 * 54 * (1 - $financialScore->score/100) }}"
                                stroke-linecap="round" />
                        </svg>
                    </div>
                </div>

                <div class="space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-zinc-400">Taxa de Poupança:</span>
                        <span class="font-medium">{{ number_format($financialScore->savings_rate, 1) }}%</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-zinc-400">Consistência:</span>
                        <span class="font-medium">{{ number_format($financialScore->consistency_score, 0) }}/20</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600 dark:text-zinc-400">Aderência às Metas:</span>
                        <span class="font-medium">{{ number_format($financialScore->budget_adherence, 0) }}/25</span>
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 text-center">
                <span class="material-icons text-6xl text-gray-300 dark:text-zinc-600 mb-4">psychology</span>
                <p class="text-gray-600 dark:text-zinc-400">Gere insights para calcular seu score</p>
            </div>
        @endif

        {{-- User Profile Card --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-4">
                Seu Perfil Financeiro
            </h3>
            
            <div class="text-center py-8">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 mb-4">
                    <span class="material-icons text-5xl text-white">person</span>
                </div>
                
                <h4 class="text-2xl font-bold text-gray-900 dark:text-zinc-100 mb-2">
                    {{ $userProfile ?: 'Analisando...' }}
                </h4>
                
                <p class="text-sm text-gray-600 dark:text-zinc-400">
                    @switch($userProfile)
                        @case('Poupador Consistente')
                            Você tem disciplina financeira exemplar! Continue assim!
                            @break
                        @case('Gastador de Fim de Semana')
                            Você tende a gastar mais nos finais de semana. Fique atento!
                            @break
                        @case('Gastador Impulsivo')
                            Seus gastos estão próximos da renda. Hora de criar metas!
                            @break
                        @case('Equilibrado')
                            Você mantém um bom equilíbrio entre gastos e poupança.
                            @break
                        @default
                            Continue usando o Perfic para construir seu perfil financeiro.
                    @endswitch
                </p>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="flex items-center justify-between mb-6">
        <div class="flex space-x-2">
            <button 
                wire:click="$set('filter', 'all')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'all' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}"
            >
                Todos
            </button>
            <button 
                wire:click="$set('filter', 'unread')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'unread' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}"
            >
                Não Lidos
            </button>
            <button 
                wire:click="$set('filter', 'high_impact')"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ $filter === 'high_impact' ? 'bg-blue-600 text-white' : 'bg-gray-100 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 hover:bg-gray-200 dark:hover:bg-zinc-600' }}"
            >
                Alto Impacto
            </button>
        </div>

        @if($insights->where('is_read', false)->count() > 0)
            <button 
                wire:click="markAllAsRead"
                class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400 dark:hover:text-blue-300"
            >
                Marcar todos como lidos
            </button>
        @endif
    </div>

    {{-- Insights List --}}
    <div class="space-y-4">
        @forelse($insights as $insight)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 {{ !$insight->is_read ? 'ring-2 ring-blue-500 ring-opacity-50' : '' }}">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center space-x-3 mb-2">
                            <span class="material-icons {{ $this->getImpactColor($insight->impact_level) }}">
                                {{ $this->getImpactIcon($insight->impact_level) }}
                            </span>
                            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $insight->impact_level === 'high' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' : ($insight->impact_level === 'medium' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200') }}">
                                {{ $this->getTypeLabel($insight->type) }}
                            </span>
                            @if(!$insight->is_read)
                                <span class="w-2 h-2 bg-blue-600 rounded-full"></span>
                            @endif
                        </div>

                        <h4 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-2">
                            {{ $insight->title }}
                        </h4>

                        <p class="text-sm text-gray-600 dark:text-zinc-400 mb-3">
                            {{ $insight->description }}
                        </p>

                        <div class="flex items-center space-x-4 text-xs text-gray-500 dark:text-zinc-500">
                            <span>{{ $insight->created_at->diffForHumans() }}</span>
                            @if($insight->category)
                                <span class="flex items-center">
                                    <span class="material-icons text-sm mr-1">{{ $insight->category->icon }}</span>
                                    {{ $insight->category->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="flex space-x-2 ml-4">
                        @if(!$insight->is_read)
                            <button 
                                wire:click="markAsRead({{ $insight->id }})"
                                class="p-2 text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 transition-colors"
                                title="Marcar como lido"
                            >
                                <span class="material-icons text-sm">done</span>
                            </button>
                        @endif
                        
                        <button 
                            wire:click="deleteInsight({{ $insight->id }})"
                            class="p-2 text-gray-400 hover:text-red-600 dark:hover:text-red-400 transition-colors"
                            title="Remover"
                        >
                            <span class="material-icons text-sm">delete</span>
                        </button>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-12 text-center">
                <span class="material-icons text-6xl text-gray-300 dark:text-zinc-600 mb-4">lightbulb</span>
                <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">
                    Nenhum insight encontrado
                </h3>
                <p class="text-gray-600 dark:text-zinc-400 mb-4">
                    Clique em "Gerar Novos Insights" para começar a análise inteligente
                </p>
            </div>
        @endforelse
    </div>
</div>