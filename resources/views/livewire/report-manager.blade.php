<div class="p-4 lg:p-6">
    {{-- Header com filtros --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-4 lg:p-6 mb-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    📊 Relatórios Financeiros
                </h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">
                    Análise completa das suas finanças
                </p>
            </div>
            
            <div class="flex items-center space-x-3">
                <button 
                    wire:click="exportReport('pdf')"
                    class="inline-flex items-center px-3 py-2 text-xs font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 dark:bg-zinc-700 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-600 transition-colors"
                >
                    <span class="material-icons text-sm mr-1">download</span>
                    Exportar PDF
                </button>
            </div>
        </div>

        {{-- Filtros --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mt-6">
            {{-- Tipo de Relatório --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    Tipo de Relatório
                </label>
                <select 
                    wire:model.live="reportType"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-100"
                >
                    <option value="summary">Resumo Geral</option>
                    <option value="category">Por Categoria</option>
                    <option value="goals">Metas</option>
                    <option value="comparison">Comparativo</option>
                </select>
            </div>

            {{-- Período --}}
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">
                    Período
                </label>
                <select 
                    wire:model.live="period"
                    class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-100"
                >
                    <option value="current_month">Mês Atual</option>
                    <option value="last_month">Mês Passado</option>
                    <option value="last_3_months">Últimos 3 Meses</option>
                    <option value="last_6_months">Últimos 6 Meses</option>
                    <option value="current_year">Ano Atual</option>
                    <option value="last_year">Ano Passado</option>
                    <option value="custom">Período Personalizado</option>
                </select>
            </div>

            {{-- Data Início (se custom) --}}
            @if($customPeriod)
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">
                        Data Início
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="startDate"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-100"
                    />
                </div>
            @endif

            {{-- Data Fim (se custom) --}}
            @if($customPeriod)
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">
                        Data Fim
                    </label>
                    <input 
                        type="date" 
                        wire:model.live="endDate"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-100"
                    />
                </div>
            @endif

            {{-- Categoria (para alguns tipos) --}}
            @if(in_array($reportType, ['summary', 'category']))
                <div>
                    <label class="block text-xs font-medium text-gray-700 dark:text-zinc-300 mb-1">
                        Categoria (Opcional)
                    </label>
                    <select 
                        wire:model.live="categoryId"
                        class="w-full px-3 py-2 text-sm border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-zinc-700 dark:border-zinc-600 dark:text-zinc-100"
                    >
                        <option value="">Todas as Categorias</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>
    </div>

    {{-- Loading --}}
    @if($loading)
        <div class="text-center py-12">
            <div class="inline-flex items-center text-gray-600 dark:text-zinc-400">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Gerando relatório...
            </div>
        </div>
    @endif

    {{-- Conteúdo do Relatório --}}
    @if(!$loading && !empty($reportData))
        
        {{-- RESUMO GERAL --}}
        @if($reportType === 'summary')
            @include('livewire.reports.summary-report')
        @endif

        {{-- POR CATEGORIA --}}
        @if($reportType === 'category')
            @include('livewire.reports.category-report')
        @endif

        {{-- METAS --}}
        @if($reportType === 'goals')
            @include('livewire.reports.goals-report')
        @endif

        {{-- COMPARATIVO --}}
        @if($reportType === 'comparison')
            @include('livewire.reports.comparison-report')
        @endif

    @endif

    {{-- Estado Vazio --}}
    @if(!$loading && empty($reportData))
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-12 text-center">
            <span class="material-icons text-6xl text-gray-300 dark:text-zinc-600 mb-4">assessment</span>
            <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">
                Nenhum dado encontrado
            </h3>
            <p class="text-gray-600 dark:text-zinc-400">
                Não há transações no período selecionado para gerar o relatório.
            </p>
        </div>
    @endif
</div>

{{-- Estilos customizados para gráficos --}}
@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Configurações globais do Chart.js
    Chart.defaults.font.family = 'Inter, system-ui, sans-serif';
    Chart.defaults.color = getComputedStyle(document.documentElement).getPropertyValue('--text-primary') || '#374151';
    Chart.defaults.borderColor = getComputedStyle(document.documentElement).getPropertyValue('--border-color') || '#E5E7EB';
});

// Função para formatar valores em MZN
function formatMZN(value) {
    return 'MZN ' + new Intl.NumberFormat('pt-MZ').format(value);
}

// Função para cores baseadas no tema
function getThemeColors() {
    const isDark = document.documentElement.classList.contains('dark');
    return {
        primary: '#3B82F6',
        success: '#10B981',
        warning: '#F59E0B',
        danger: '#EF4444',
        text: isDark ? '#F9FAFB' : '#111827',
        background: isDark ? '#1F2937' : '#FFFFFF'
    };
}
</script>
@endsection