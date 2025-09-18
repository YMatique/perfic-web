{{-- Cards de Resumo --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    {{-- Total de Receitas --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Receitas</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">
                    MZN {{ number_format($reportData['summary']['total_income'], 2, ',', '.') }}
                </p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-green-600 dark:text-green-400">trending_up</span>
            </div>
        </div>
    </div>

    {{-- Total de Despesas --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Despesas</p>
                <p class="text-2xl font-bold text-red-600 dark:text-red-400">
                    MZN {{ number_format($reportData['summary']['total_expenses'], 2, ',', '.') }}
                </p>
            </div>
            <div class="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-red-600 dark:text-red-400">trending_down</span>
            </div>
        </div>
    </div>

    {{-- Saldo Líquido --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Saldo Líquido</p>
                <p class="text-2xl font-bold {{ $reportData['summary']['net_balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    MZN {{ number_format($reportData['summary']['net_balance'], 2, ',', '.') }}
                </p>
            </div>
            <div class="w-12 h-12 {{ $reportData['summary']['net_balance'] >= 0 ? 'bg-green-100 dark:bg-green-900' : 'bg-red-100 dark:bg-red-900' }} rounded-lg flex items-center justify-center">
                <span class="material-icons {{ $reportData['summary']['net_balance'] >= 0 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                    account_balance_wallet
                </span>
            </div>
        </div>
    </div>

    {{-- Taxa de Poupança --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Taxa de Poupança</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">
                    {{ number_format($reportData['summary']['savings_rate'], 1) }}%
                </p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-blue-600 dark:text-blue-400">savings</span>
            </div>
        </div>
    </div>
</div>

{{-- Estatísticas Adicionais --}}
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-4">Estatísticas do Período</h3>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-zinc-400">Total de Transações:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $reportData['summary']['transactions_count'] }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-zinc-400">Gasto Médio Diário:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                    MZN {{ number_format($reportData['summary']['daily_average_expenses'], 2, ',', '.') }}
                </span>
            </div>
            <div class="flex justify-between">
                <span class="text-sm text-gray-600 dark:text-zinc-400">Período:</span>
                <span class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                    {{ $reportData['period']['days'] }} dias
                </span>
            </div>
        </div>
    </div>

    {{-- Gráfico de Evolução Mensal --}}
    <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-4">Evolução Mensal</h3>
        <canvas id="monthlyEvolutionChart" width="400" height="200"></canvas>
    </div>
</div>

{{-- Breakdown por Categorias --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    {{-- Receitas por Categoria --}}
    @if(!empty($reportData['category_breakdown']['income']) && count($reportData['category_breakdown']['income']) > 0)
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100">Receitas por Categoria</h3>
            <span class="text-xs text-gray-500 dark:text-zinc-400">{{ count($reportData['category_breakdown']['income']) }} categorias</span>
        </div>
        
        <div class="space-y-4">
            @foreach($reportData['category_breakdown']['income'] as $category)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $category['category_color'] }}20; color: {{ $category['category_color'] }};">
                            <span class="material-icons text-sm">{{ $category['category_icon'] }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $category['category_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $category['count'] }} transações</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-green-600 dark:text-green-400">
                            MZN {{ number_format($category['total'], 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">{{ number_format($category['percentage'], 1) }}%</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Despesas por Categoria --}}
    @if(!empty($reportData['category_breakdown']['expense']) && count($reportData['category_breakdown']['expense']) > 0)
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between mb-6">
            <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100">Despesas por Categoria</h3>
            <span class="text-xs text-gray-500 dark:text-zinc-400">{{ count($reportData['category_breakdown']['expense']) }} categorias</span>
        </div>
        
        <div class="space-y-4">
            @foreach($reportData['category_breakdown']['expense'] as $category)
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center" style="background-color: {{ $category['category_color'] }}20; color: {{ $category['category_color'] }};">
                            <span class="material-icons text-sm">{{ $category['category_icon'] }}</span>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">{{ $category['category_name'] }}</p>
                            <p class="text-xs text-gray-500 dark:text-zinc-400">{{ $category['count'] }} transações</p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-medium text-red-600 dark:text-red-400">
                            MZN {{ number_format($category['total'], 2, ',', '.') }}
                        </p>
                        <p class="text-xs text-gray-500 dark:text-zinc-400">{{ number_format($category['percentage'], 1) }}%</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

{{-- Top Transações --}}
@if(!empty($reportData['top_transactions']) && count($reportData['top_transactions']) > 0)
<div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
    <div class="flex items-center justify-between mb-6">
        <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100">Maiores Transações do Período</h3>
        <span class="text-xs text-gray-500 dark:text-zinc-400">Top {{ count($reportData['top_transactions']) }}</span>
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
                @foreach($reportData['top_transactions'] as $transaction)
                    <tr>
                        <td class="py-3">
                            <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                {{ $transaction['description'] }}
                            </p>
                        </td>
                        <td class="py-3">
                            <div class="flex items-center space-x-2">
                                <span class="material-icons text-sm text-gray-400">{{ $transaction['category_icon'] }}</span>
                                <span class="text-sm text-gray-600 dark:text-zinc-400">{{ $transaction['category_name'] }}</span>
                            </div>
                        </td>
                        <td class="py-3">
                            <span class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}
                            </span>
                        </td>
                        <td class="py-3 text-right">
                            <span class="text-sm font-medium {{ $transaction['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                MZN {{ number_format($transaction['amount'], 2, ',', '.') }}
                            </span>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- Script para o gráfico --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Dados do gráfico de evolução mensal
    const monthlyData = @json($reportData['monthly_evolution']);
    
    const ctx = document.getElementById('monthlyEvolutionChart');
    if (ctx) {
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: monthlyData.map(item => item.month),
                datasets: [
                    {
                        label: 'Receitas',
                        data: monthlyData.map(item => item.income),
                        borderColor: '#10B981',
                        backgroundColor: '#10B98120',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Despesas',
                        data: monthlyData.map(item => item.expenses),
                        borderColor: '#EF4444',
                        backgroundColor: '#EF444420',
                        tension: 0.4,
                        fill: false
                    },
                    {
                        label: 'Saldo',
                        data: monthlyData.map(item => item.balance),
                        borderColor: '#3B82F6',
                        backgroundColor: '#3B82F620',
                        tension: 0.4,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
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
                },
                elements: {
                    point: {
                        radius: 4,
                        hoverRadius: 6
                    }
                }
            }
        });
    }
});
</script>
@endpush