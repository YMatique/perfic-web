<div class="p-4 lg:p-6 ">
    {{-- Header com Saudação --}}
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-zinc-100">
            Olá, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-600 dark:text-zinc-400 mt-1">
            Aqui está seu resumo financeiro de {{ now()->format('F Y') }}
        </p>
    </div>

    {{-- Cards de Resumo Financeiro --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        {{-- Receitas --}}
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-green-100 text-sm font-medium">Receitas</p>
                    <h3 class="text-2xl font-bold mt-2">
                        MZN {{ number_format($summaryData['current_income'] ?? 0, 2, ',', '.') }}
                    </h3>
                    @if(($summaryData['income_change'] ?? 0) != 0)
                        <p class="text-sm text-green-100 mt-2">
                            @if($summaryData['income_change'] > 0) ↗ @else ↘ @endif
                            {{ number_format(abs($summaryData['income_change']), 1) }}% vs mês passado
                        </p>
                    @endif
                </div>
                <div class="bg-green-200 text-green-800 bg-opacity-20 rounded-lg p-3">
                    <span class="material-icons text-2xl">trending_up</span>
                </div>
            </div>
        </div>

        {{-- Despesas --}}
        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-red-100 text-sm font-medium">Despesas</p>
                    <h3 class="text-2xl font-bold mt-2">
                        MZN {{ number_format($summaryData['current_expenses'] ?? 0, 2, ',', '.') }}
                    </h3>
                    @if(($summaryData['expense_change'] ?? 0) != 0)
                        <p class="text-sm text-red-100 mt-2">
                            @if($summaryData['expense_change'] > 0) ↗ @else ↘ @endif
                            {{ number_format(abs($summaryData['expense_change']), 1) }}% vs mês passado
                        </p>
                    @endif
                </div>
                <div class="bg-red-200/90 text-red-600 bg-opacity-20 rounded-lg p-3">
                    <span class="material-icons text-2xl">trending_down</span>
                </div>
            </div>
        </div>

        {{-- Saldo --}}
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-blue-100 text-sm font-medium">Saldo do Mês</p>
                    <h3 class="text-2xl font-bold mt-2">
                        MZN {{ number_format($summaryData['current_balance'] ?? 0, 2, ',', '.') }}
                    </h3>
                    <p class="text-sm text-blue-100 mt-2">
                        {{ ($summaryData['transactions_count'] ?? 0) }} transações
                    </p>
                </div>
                <div class="bg-blue-200/20 text-blue-900 bg-opacity-20 rounded-lg p-3">
                    <span class="material-icons text-3xl">account_balance_wallet</span>
                </div>
            </div>
        </div>

        {{-- Taxa de Poupança --}}
        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl shadow-lg p-6 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-purple-100 text-sm font-medium">Taxa de Poupança</p>
                    <h3 class="text-2xl font-bold mt-2">
                        {{ number_format($summaryData['savings_rate'] ?? 0, 1) }}%
                    </h3>
                    @if($financialScore)
                        <p class="text-sm text-purple-100 mt-2">
                            Score: {{ $financialScore->score }}/100
                        </p>
                    @endif
                </div>
                <div class=" bg-purple-200 text-purple-800 bg-opacity-20 rounded-lg p-3">
                    <span class="material-icons text-3xl">savings</span>
                </div>
            </div>
        </div>
    </div>

    {{-- Grid Principal --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
        {{-- Gráfico de Tendências (2/3 da largura) --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    Tendência dos Últimos 6 Meses
                </h3>
                <button wire:click="refreshData" class="text-sm text-blue-600 hover:text-blue-700 dark:text-blue-400">
                    <span class="material-icons text-sm">refresh</span>
                </button>
            </div>
            <div style="height: 300px; position: relative;">
                <canvas id="trendsChart"></canvas>
            </div>
        </div>

        {{-- Top Categorias (1/3 da largura) --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-6">
                Top Categorias
            </h3>
            <div class="space-y-4">
                @forelse($topCategories as $category)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3 flex-1">
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" 
                                 style="background-color: {{ $category['color'] }}20; color: {{ $category['color'] }};">
                                <span class="material-icons text-sm">{{ $category['icon'] }}</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100 truncate">
                                    {{ $category['name'] }}
                                </p>
                                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2 mt-1">
                                    <div class="h-2 rounded-full transition-all" 
                                         style="width: {{ $category['percentage'] }}%; background-color: {{ $category['color'] }};">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-right ml-3">
                            <p class="text-sm font-semibold text-gray-900 dark:text-zinc-100">
                                {{ number_format($category['percentage'], 0) }}%
                            </p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400">
                                MZN {{ number_format($category['total'], 0) }}
                            </p>
                        </div>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-zinc-400 py-8">Nenhuma despesa este mês</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Metas e Insights --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
        {{-- Metas Ativas --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    Metas Ativas
                </h3>
                <a href="{{ route('goals') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">
                    Ver todas →
                </a>
            </div>
            <div class="space-y-4">
                @forelse($activeGoals as $goal)
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $goal['name'] }}</p>
                            <span class="text-sm font-semibold {{ $goal['status'] === 'completed' ? 'text-green-600' : ($goal['status'] === 'on_track' ? 'text-blue-600' : 'text-yellow-600') }}">
                                {{ number_format($goal['percentage'], 0) }}%
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="h-2 rounded-full transition-all {{ $goal['status'] === 'completed' ? 'bg-green-500' : ($goal['status'] === 'on_track' ? 'bg-blue-500' : 'bg-yellow-500') }}" 
                                 style="width: {{ min($goal['percentage'], 100) }}%">
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 dark:text-zinc-400 mt-1">
                            MZN {{ number_format($goal['current_progress'], 0) }} / MZN {{ number_format($goal['target_amount'], 0) }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-zinc-400 py-8">Nenhuma meta ativa</p>
                @endforelse
            </div>
        </div>

        {{-- Insights de IA --}}
        <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                    💡 Insights de IA
                </h3>
                <a href="{{ route('insights') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">
                    Ver todos →
                </a>
            </div>
            <div class="space-y-3">
                @forelse($aiInsights as $insight)
                    <div class="p-4 rounded-lg {{ $insight['impact_level'] === 'high' ? 'bg-red-50 dark:bg-red-900/20' : ($insight['impact_level'] === 'medium' ? 'bg-yellow-50 dark:bg-yellow-900/20' : 'bg-blue-50 dark:bg-blue-900/20') }}">
                        <p class="text-sm font-medium {{ $insight['impact_level'] === 'high' ? 'text-red-900 dark:text-red-200' : ($insight['impact_level'] === 'medium' ? 'text-yellow-900 dark:text-yellow-200' : 'text-blue-900 dark:text-blue-200') }}">
                            {{ $insight['title'] }}
                        </p>
                        <p class="text-xs {{ $insight['impact_level'] === 'high' ? 'text-red-700 dark:text-red-300' : ($insight['impact_level'] === 'medium' ? 'text-yellow-700 dark:text-yellow-300' : 'text-blue-700 dark:text-blue-300') }} mt-1">
                            {{ Str::limit($insight['description'], 80) }}
                        </p>
                    </div>
                @empty
                    <p class="text-center text-gray-500 dark:text-zinc-400 py-8">Nenhum insight novo</p>
                @endforelse
            </div>
        </div>
    </div>

    {{-- Transações Recentes --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">
                Transações Recentes
            </h3>
            <a href="{{ route('transactions') }}" wire:navigate class="text-sm text-blue-600 hover:text-blue-700">
                Ver todas →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-gray-200 dark:border-zinc-700">
                        <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Descrição</th>
                        <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Categoria</th>
                        <th class="text-left py-3 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Data</th>
                        <th class="text-right py-3 text-xs font-medium text-gray-500 dark:text-zinc-400 uppercase">Valor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-zinc-700">
                    @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50 dark:hover:bg-zinc-700/50 transition-colors">
                            <td class="py-3">
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                    {{ $transaction['description'] }}
                                </p>
                            </td>
                            <td class="py-3">
                                <div class="flex items-center space-x-2">
                                    <div class="w-8 h-8 rounded-lg flex items-center justify-center" 
                                         style="background-color: {{ $transaction['category_color'] }}20; color: {{ $transaction['category_color'] }};">
                                        <span class="material-icons text-sm">{{ $transaction['category_icon'] }}</span>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-zinc-400">{{ $transaction['category_name'] }}</span>
                                </div>
                            </td>
                            <td class="py-3">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">
                                    {{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="py-3 text-right">
                                <span class="text-sm font-semibold {{ $transaction['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                    {{ $transaction['type'] === 'income' ? '+' : '-' }} 
                                    MZN {{ number_format($transaction['amount'], 2, ',', '.') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center text-gray-500 dark:text-zinc-400">
                                Nenhuma transação recente
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- Script para o gráfico de tendências --}}
@section('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
function formatMZN(value) {
    return 'MZN ' + new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 0,
        maximumFractionDigits: 0
    }).format(value);
}

document.addEventListener('DOMContentLoaded', function() {
    const trendsData = @json($monthlyTrends);
    
    const ctx = document.getElementById('trendsChart');
    if (ctx && trendsData.length > 0) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: trendsData.map(item => item.month + '/' + item.year.substr(-2)),
                datasets: [
                    {
                        label: 'Receitas',
                        data: trendsData.map(item => item.income),
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3
                    },
                    {
                        label: 'Despesas',
                        data: trendsData.map(item => item.expenses),
                        borderColor: '#EF4444',
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        tension: 0.4,
                        fill: true,
                        borderWidth: 3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 15
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': ' + formatMZN(context.parsed.y);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatMZN(value);
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                },
                interaction: {
                    intersect: false,
                    mode: 'index'
                }
            }
        });
    }
});
</script>
@endsection