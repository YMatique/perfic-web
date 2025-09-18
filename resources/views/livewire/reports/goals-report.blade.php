{{-- Header com estatísticas das metas --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Total de Metas</p>
                <p class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $reportData['summary']['total_goals'] }}</p>
            </div>
            <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-blue-600 dark:text-blue-400">flag</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Metas Ativas</p>
                <p class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $reportData['summary']['active_goals'] }}</p>
            </div>
            <div class="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-green-600 dark:text-green-400">check_circle</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Concluídas</p>
                <p class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $reportData['summary']['completed_goals'] }}</p>
            </div>
            <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-purple-600 dark:text-purple-400">emoji_events</span>
            </div>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Progresso Médio</p>
                <p class="text-2xl font-bold text-orange-600 dark:text-orange-400">{{ number_format($reportData['summary']['average_progress'], 1) }}%</p>
            </div>
            <div class="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center">
                <span class="material-icons text-orange-600 dark:text-orange-400">trending_up</span>
            </div>
        </div>
    </div>
</div>

{{-- Lista de Metas --}}
@if(!empty($reportData['goals']) && count($reportData['goals']) > 0)
    <div class="space-y-6">
        @foreach($reportData['goals'] as $goal)
            <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6">
                {{-- Header da Meta --}}
                <div class="flex items-center justify-between mb-6">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 rounded-lg flex items-center justify-center 
                            @if($goal['status'] === 'completed') bg-green-100 dark:bg-green-900 
                            @elseif($goal['status'] === 'warning') bg-yellow-100 dark:bg-yellow-900 
                            @elseif($goal['status'] === 'on_track') bg-blue-100 dark:bg-blue-900 
                            @else bg-gray-100 dark:bg-gray-900 
                            @endif">
                            <span class="material-icons 
                                @if($goal['status'] === 'completed') text-green-600 dark:text-green-400 
                                @elseif($goal['status'] === 'warning') text-yellow-600 dark:text-yellow-400 
                                @elseif($goal['status'] === 'on_track') text-blue-600 dark:text-blue-400 
                                @else text-gray-600 dark:text-gray-400 
                                @endif">
                                {{ $goal['category_icon'] ?? 'flag' }}
                            </span>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">{{ $goal['name'] }}</h3>
                            <div class="flex items-center space-x-4 mt-1">
                                <span class="text-sm text-gray-600 dark:text-zinc-400">
                                    @switch($goal['type'])
                                        @case('spending_limit') Limite de Gastos @break
                                        @case('savings_target') Meta de Poupança @break
                                        @case('category_limit') Limite de Categoria @break
                                        @case('income_target') Meta de Receita @break
                                    @endswitch
                                </span>
                                @if($goal['category_name'])
                                    <span class="text-sm text-gray-600 dark:text-zinc-400">• {{ $goal['category_name'] }}</span>
                                @endif
                                <span class="text-sm text-gray-600 dark:text-zinc-400">• {{ ucfirst($goal['period']) }}</span>
                                @if(!$goal['is_active'])
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded text-xs">Inativa</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600 dark:text-zinc-400">
                            @if($goal['status'] === 'completed')
                                <span class="text-green-600 dark:text-green-400 font-medium">✓ Concluída</span>
                            @elseif($goal['status'] === 'warning')
                                <span class="text-yellow-600 dark:text-yellow-400 font-medium">⚠ Atenção</span>
                            @elseif($goal['status'] === 'on_track')
                                <span class="text-blue-600 dark:text-blue-400 font-medium">↗ No Caminho</span>
                            @else
                                <span class="text-gray-600 dark:text-gray-400 font-medium">◯ Abaixo da Meta</span>
                            @endif
                        </p>
                        <p class="text-xs text-gray-500 dark:text-zinc-500 mt-1">
                            {{ \Carbon\Carbon::parse($goal['start_date'])->format('d/m/Y') }} - 
                            {{ \Carbon\Carbon::parse($goal['end_date'])->format('d/m/Y') }}
                        </p>
                    </div>
                </div>

                {{-- Barra de Progresso --}}
                <div class="mb-6">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm font-medium text-gray-900 dark:text-zinc-100">Progresso</span>
                        <span class="text-sm font-medium 
                            @if($goal['percentage'] >= 100) text-green-600 dark:text-green-400
                            @elseif($goal['percentage'] >= 80) text-yellow-600 dark:text-yellow-400
                            @elseif($goal['percentage'] >= 50) text-blue-600 dark:text-blue-400
                            @else text-gray-600 dark:text-gray-400
                            @endif">
                            {{ number_format($goal['percentage'], 1) }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-300
                            @if($goal['percentage'] >= 100) bg-green-500
                            @elseif($goal['percentage'] >= 80) bg-yellow-500
                            @elseif($goal['percentage'] >= 50) bg-blue-500
                            @else bg-gray-400
                            @endif" 
                            style="width: {{ min($goal['percentage'], 100) }}%">
                        </div>
                    </div>
                </div>

                {{-- Valores --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="text-center p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Meta</p>
                        <p class="text-lg font-bold text-gray-900 dark:text-zinc-100">
                            MZN {{ number_format($goal['target_amount'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">Atual</p>
                        <p class="text-lg font-bold 
                            @if($goal['type'] === 'savings_target' || $goal['type'] === 'income_target') 
                                text-green-600 dark:text-green-400
                            @else 
                                text-red-600 dark:text-red-400
                            @endif">
                            MZN {{ number_format($goal['current_progress'], 2, ',', '.') }}
                        </p>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-zinc-700 rounded-lg">
                        <p class="text-xs font-medium text-gray-600 dark:text-zinc-400 mb-1">
                            @if($goal['percentage'] >= 100)
                                Excedente
                            @else
                                Restante
                            @endif
                        </p>
                        <p class="text-lg font-bold 
                            @if($goal['percentage'] >= 100)
                                text-orange-600 dark:text-orange-400
                            @else
                                text-blue-600 dark:text-blue-400
                            @endif">
                            MZN {{ number_format(abs($goal['remaining']), 2, ',', '.') }}
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Gráfico de Progresso das Metas --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-6 mt-8">
        <h3 class="text-sm font-medium text-gray-900 dark:text-zinc-100 mb-6">Progresso de Todas as Metas</h3>
        <canvas id="goalsProgressChart" width="400" height="300"></canvas>
    </div>
@else
    {{-- Estado vazio --}}
    <div class="bg-white dark:bg-zinc-800 rounded-lg shadow-sm border border-gray-200 dark:border-zinc-700 p-12 text-center">
        <span class="material-icons text-6xl text-gray-300 dark:text-zinc-600 mb-4">flag</span>
        <h3 class="text-lg font-medium text-gray-900 dark:text-zinc-100 mb-2">
            Nenhuma meta encontrada
        </h3>
        <p class="text-gray-600 dark:text-zinc-400">
            Não há metas definidas para o período selecionado.
        </p>
    </div>
@endif

{{-- Script para gráfico de metas --}}
@section('scripts')
<script>
    function formatMZN(value) {
    return 'MZN ' + new Intl.NumberFormat('pt-MZ', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(value);
}
document.addEventListener('DOMContentLoaded', function() {
    @if(!empty($reportData['goals']) && count($reportData['goals']) > 0)
        const goalsData = @json($reportData['goals']);
        
        const ctx = document.getElementById('goalsProgressChart');
        if (ctx && goalsData.length > 0) {
            // Preparar dados para o gráfico de barras horizontais
            const labels = goalsData.map(goal => {
                let name = goal.name;
                if (name.length > 25) {
                    name = name.substring(0, 25) + '...';
                }
                return name;
            });
            
            const progressData = goalsData.map(goal => goal.percentage);
            const backgroundColors = goalsData.map(goal => {
                if (goal.percentage >= 100) return '#10B981'; // Green
                if (goal.percentage >= 80) return '#F59E0B';  // Yellow
                if (goal.percentage >= 50) return '#3B82F6';  // Blue
                return '#6B7280'; // Gray
            });
            
            new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Progresso (%)',
                        data: progressData,
                        backgroundColor: backgroundColors,
                        borderColor: backgroundColors,
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    indexAxis: 'y', // Barras horizontais
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        x: {
                            beginAtZero: true,
                            max: Math.max(100, Math.max(...progressData)),
                            ticks: {
                                callback: function(value) {
                                    return value + '%';
                                }
                            }
                        },
                        y: {
                            ticks: {
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });
        }
    @endif
});
</script>
@endsection