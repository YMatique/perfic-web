{{-- Header com comparação --}}
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 mb-8">
    <div class="text-center mb-6">
        <h2 class="text-xl font-semibold text-gray-900 dark:text-zinc-100">Comparação de Períodos</h2>
        <div class="flex items-center justify-center space-x-8 mt-4 text-sm text-gray-600 dark:text-zinc-400">
            <div class="text-center">
                <p class="font-medium">Período Atual</p>
                <p>{{ $reportData['current_period']['period']['start'] }} - {{ $reportData['current_period']['period']['end'] }}</p>
                <p class="text-xs">({{ $reportData['current_period']['period']['days'] }} dias)</p>
            </div>
            <div class="text-center">
                <span class="material-icons text-blue-500">compare_arrows</span>
            </div>
            <div class="text-center">
                <p class="font-medium">Período Anterior</p>
                <p>{{ $reportData['previous_period']['period']['start'] }} - {{ $reportData['previous_period']['period']['end'] }}</p>
                <p class="text-xs">({{ $reportData['previous_period']['period']['days'] }} dias)</p>
            </div>
        </div>
    </div>
</div>

{{-- Cards de Comparação --}}
<div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
    {{-- Comparação de Receitas --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Receitas</h3>
            <div class="flex items-center space-x-2">
                <span class="material-icons text-sm {{ $reportData['comparison']['income_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $reportData['comparison']['income_change'] >= 0 ? 'trending_up' : 'trending_down' }}
                </span>
                <span class="text-sm font-medium {{ $reportData['comparison']['income_change'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $reportData['comparison']['income_change'] >= 0 ? '+' : '' }}{{ number_format($reportData['comparison']['income_change'], 1) }}%
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                <p class="text-xs font-medium text-green-700 dark:text-green-400 mb-1">Atual</p>
                <p class="text-lg font-bold text-green-600 dark:text-green-400">
                    MZN {{ number_format($reportData['current_period']['summary']['total_income'], 2, ',', '.') }}
                </p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Anterior</p>
                <p class="text-lg font-bold text-gray-900 dark:text-zinc-100">
                    MZN {{ number_format($reportData['previous_period']['summary']['total_income'], 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="mt-4 p-3 {{ $reportData['comparison']['income_change_absolute'] >= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }} rounded-lg">
            <p class="text-sm {{ $reportData['comparison']['income_change_absolute'] >= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                <span class="font-medium">
                    {{ $reportData['comparison']['income_change_absolute'] >= 0 ? 'Aumento' : 'Redução' }} de 
                    MZN {{ number_format(abs($reportData['comparison']['income_change_absolute']), 2, ',', '.') }}
                </span>
                em relação ao período anterior
            </p>
        </div>
    </div>

    {{-- Comparação de Despesas --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Despesas</h3>
            <div class="flex items-center space-x-2">
                <span class="material-icons text-sm {{ $reportData['comparison']['expense_change'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $reportData['comparison']['expense_change'] >= 0 ? 'trending_up' : 'trending_down' }}
                </span>
                <span class="text-sm font-medium {{ $reportData['comparison']['expense_change'] <= 0 ? 'text-green-600' : 'text-red-600' }}">
                    {{ $reportData['comparison']['expense_change'] >= 0 ? '+' : '' }}{{ number_format($reportData['comparison']['expense_change'], 1) }}%
                </span>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                <p class="text-xs font-medium text-red-700 dark:text-red-400 mb-1">Atual</p>
                <p class="text-lg font-bold text-red-600 dark:text-red-400">
                    MZN {{ number_format($reportData['current_period']['summary']['total_expenses'], 2, ',', '.') }}
                </p>
            </div>
            <div class="text-center p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Anterior</p>
                <p class="text-lg font-bold text-gray-900 dark:text-zinc-100">
                    MZN {{ number_format($reportData['previous_period']['summary']['total_expenses'], 2, ',', '.') }}
                </p>
            </div>
        </div>

        <div class="mt-4 p-3 {{ $reportData['comparison']['expense_change_absolute'] <= 0 ? 'bg-green-50 dark:bg-green-900/20' : 'bg-red-50 dark:bg-red-900/20' }} rounded-lg">
            <p class="text-sm {{ $reportData['comparison']['expense_change_absolute'] <= 0 ? 'text-green-800 dark:text-green-300' : 'text-red-800 dark:text-red-300' }}">
                <span class="font-medium">
                    {{ $reportData['comparison']['expense_change_absolute'] >= 0 ? 'Aumento' : 'Redução' }} de 
                    MZN {{ number_format(abs($reportData['comparison']['expense_change_absolute']), 2, ',', '.') }}
                </span>
                nas despesas
            </p>
        </div>
    </div>
</div>

{{-- Gráfico de Comparação Lado a Lado --}}
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 mb-8">
    <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-6">Comparação Visual</h3>
    <canvas id="comparisonChart" width="400" height="250"></canvas>
</div>

{{-- Comparação Detalhada por Categorias --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    {{-- Categorias Período Atual --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-6">
            Gastos por Categoria - Atual
        </h3>
        
        @if(!empty($reportData['current_period']['category_breakdown']['expense']))
            <div class="space-y-4">
                @foreach($reportData['current_period']['category_breakdown']['expense'] as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $category['category_color'] }}20; color: {{ $category['category_color'] }};">
                                <span class="material-icons text-sm">{{ $category['category_icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $category['category_name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">{{ number_format($category['percentage'], 1) }}%</p>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-red-600 dark:text-red-400">
                            MZN {{ number_format($category['total'], 2, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-zinc-400 py-8">Nenhuma despesa no período atual</p>
        @endif
    </div>

    {{-- Categorias Período Anterior --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-6">
            Gastos por Categoria - Anterior
        </h3>
        
        @if(!empty($reportData['previous_period']['category_breakdown']['expense']))
            <div class="space-y-4">
                @foreach($reportData['previous_period']['category_breakdown']['expense'] as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $category['category_color'] }}20; color: {{ $category['category_color'] }};">
                                <span class="material-icons text-sm">{{ $category['category_icon'] }}</span>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $category['category_name'] }}</p>
                                <p class="text-xs text-gray-500 dark:text-zinc-400">{{ number_format($category['percentage'], 1) }}%</p>
                            </div>
                        </div>
                        <span class="text-sm font-medium text-red-600 dark:text-red-400">
                            MZN {{ number_format($category['total'], 2, ',', '.') }}
                        </span>
                    </div>
                @endforeach
            </div>
        @else
            <p class="text-center text-gray-500 dark:text-zinc-400 py-8">Nenhuma despesa no período anterior</p>
        @endif
    </div>
</div>

{{-- Script para o gráfico de comparação --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('comparisonChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Receitas', 'Despesas', 'Saldo Líquido'],
                datasets: [
                    {
                        label: 'Período Atual',
                        data: [
                            {{ $reportData['current_period']['summary']['total_income'] }},
                            {{ $reportData['current_period']['summary']['total_expenses'] }},
                            {{ $reportData['current_period']['summary']['net_balance'] }}
                        ],
                        backgroundColor: ['#10B981', '#EF4444', '#3B82F6'],
                        borderColor: ['#10B981', '#EF4444', '#3B82F6'],
                        borderWidth: 1
                    },
                    {
                        label: 'Período Anterior',
                        data: [
                            {{ $reportData['previous_period']['summary']['total_income'] }},
                            {{ $reportData['previous_period']['summary']['total_expenses'] }},
                            {{ $reportData['previous_period']['summary']['net_balance'] }}
                        ],
                        backgroundColor: ['#10B98160', '#EF444460', '#3B82F660'],
                        borderColor: ['#10B981', '#EF4444', '#3B82F6'],
                        borderWidth: 1
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatMZN(value);
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@endpush