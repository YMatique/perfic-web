{{-- Relatório por Categoria --}}
<div class="space-y-6">
    @if(!empty($reportData) && count($reportData) > 0)
        @foreach($reportData as $category)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
                {{-- Header da Categoria --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background-color: {{ $category['color'] }}20; color: {{ $category['color'] }};">
                            <span class="material-icons text-xl">{{ $category['icon'] }}</span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $category['name'] }}</h3>
                            <p class="text-sm text-gray-600 dark:text-zinc-400">
                                {{ $category['type'] === 'income' ? 'Receita' : 'Despesa' }} • {{ $category['count'] }} transações
                            </p>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold {{ $category['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            MZN {{ number_format($category['total'], 2, ',', '.') }}
                        </p>
                        <p class="text-sm text-gray-500 dark:text-zinc-400">
                            Média: MZN {{ number_format($category['average_per_transaction'], 2, ',', '.') }}
                        </p>
                    </div>
                </div>

                {{-- Grid com Gráfico e Transações Recentes --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    {{-- Gráfico de Evolução Mensal --}}
                    <div class="" wire:ignore >
                        <h4 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-4">Evolução Mensal</h4>
                        <canvas id="categoryChart_{{ $category['id'] }}" width="400" height="200"></canvas>
                    </div>

                    {{-- Transações Recentes --}}
                    <div>
                        <h4 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-4">Transações Recentes</h4>
                        <div class="space-y-3 max-h-48 overflow-y-auto">
                            @foreach($category['recent_transactions'] as $transaction)
                                <div class="flex items-center justify-between py-2 px-3 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                                    <div>
                                        <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">
                                            {{ $transaction['description'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-zinc-400">
                                            {{ \Carbon\Carbon::parse($transaction['date'])->format('d/m/Y') }}
                                        </p>
                                    </div>
                                    <span class="text-sm font-medium {{ $category['type'] === 'income' ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        MZN {{ number_format($transaction['amount'], 2, ',', '.') }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @else
        {{-- Estado vazio --}}
        <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-12 text-center">
            <span class="material-icons text-6xl text-gray-300 dark:text-zinc-600 mb-4">category</span>
            <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">
                Nenhuma categoria encontrada
            </h3>
            <p class="text-gray-600 dark:text-zinc-400">
                Não há transações nas categorias do período selecionado.
            </p>
        </div>
    @endif
</div>

{{-- Scripts para gráficos das categorias --}}
@section('scripts')
<script>
    function formatMZN(value) {
    return 'MZN ' + new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}
document.addEventListener('DOMContentLoaded', function() {

    const categoriesData = @json($reportData);
    
    categoriesData.forEach(function(category) {
        const ctx = document.getElementById('categoryChart_' + category.id);
        if (ctx) {
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: category.monthly_evolution.map(item => item.month),
                    datasets: [{
                        label: 'Total',
                        data: category.monthly_evolution.map(item => item.total),
                        backgroundColor: category.color + '40',
                        borderColor: category.color,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
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
});
</script>
@endsection