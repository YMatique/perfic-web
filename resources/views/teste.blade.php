<x-layouts.perfic-layout title="Dashboard" pageTitle="Dashboard">
    {{-- <div class="p-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-4">
            Dashboard Perfic
        </h1>
        
    </div> --}}

    <!-- Main Content -->
    <div id="main-content" >
        <!-- Header -->
        <header class="p-4 lg:p-6">
            <div>
                <h2 class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100">Dashboard</h2>
                <p class="text-sm text-gray-600 dark:text-zinc-400 mt-1">Bem-vindo de volta! Aqui está um resumo das suas finanças.</p>
            </div>
        </header>

        <!-- Dashboard Content -->
        <main class="px-4 lg:px-6 pb-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Saldo Total -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Saldo Total</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100 mt-1">MZN 45.230,50</p>
                            <div class="flex items-center mt-2">
                                <span class="material-icons text-success-500 text-sm">trending_up</span>
                                <span class="text-success-600 dark:text-success-400 text-sm font-medium ml-1">+12,5%</span>
                                <span class="text-gray-500 dark:text-zinc-400 text-xs lg:text-sm ml-2">vs mês anterior</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                            <span class="material-icons text-white text-lg lg:text-xl">account_balance_wallet</span>
                        </div>
                    </div>
                </div>

                <!-- Receitas -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Receitas (Março)</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100 mt-1">MZN 35.000,00</p>
                            <div class="flex items-center mt-2">
                                <span class="material-icons text-success-500 text-sm">trending_up</span>
                                <span class="text-success-600 dark:text-success-400 text-sm font-medium ml-1">+5,2%</span>
                                <span class="text-gray-500 dark:text-zinc-400 text-xs lg:text-sm ml-2">vs fevereiro</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-success-500 to-success-600 rounded-xl flex items-center justify-center">
                            <span class="material-icons text-white text-lg lg:text-xl">arrow_upward</span>
                        </div>
                    </div>
                </div>

                <!-- Gastos -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Gastos (Março)</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100 mt-1">MZN 22.150,00</p>
                            <div class="flex items-center mt-2">
                                <span class="material-icons text-danger-500 text-sm">trending_down</span>
                                <span class="text-danger-600 dark:text-danger-400 text-sm font-medium ml-1">+8,1%</span>
                                <span class="text-gray-500 dark:text-zinc-400 text-xs lg:text-sm ml-2">vs fevereiro</span>
                            </div>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-danger-500 to-danger-600 rounded-xl flex items-center justify-center">
                            <span class="material-icons text-white text-lg lg:text-xl">arrow_downward</span>
                        </div>
                    </div>
                </div>

                <!-- Score Financeiro -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-zinc-400">Score Financeiro</p>
                            <p class="text-xl lg:text-2xl font-semibold text-gray-900 dark:text-zinc-100 mt-1">87/100</p>
                            <div class="flex items-center mt-2">
                                <div class="w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-success-500 to-success-600 h-2 rounded-full" style="width: 87%"></div>
                                </div>
                            </div>
                        </div>
                        <div class="w-10 h-10 lg:w-12 lg:h-12 bg-gradient-to-br from-warning-500 to-warning-600 rounded-xl flex items-center justify-center">
                            <span class="material-icons text-white text-lg lg:text-xl">emoji_events</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts and Insights Row -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 lg:gap-6 mb-6 lg:mb-8">
                <!-- Spending Chart -->
                <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100 dark:border-zinc-700">
                    <div class="flex items-center justify-between mb-4 lg:mb-6">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Gastos por Categoria</h3>
                        <select class="text-sm border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:border-primary-500">
                            <option>Últimos 30 dias</option>
                            <option>Últimos 3 meses</option>
                            <option>Este ano</option>
                        </select>
                    </div>
                    <div class="h-64 lg:h-80">
                        <canvas id="spendingChart"></canvas>
                    </div>
                </div>

                <!-- AI Insights -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm p-4 lg:p-6 border border-gray-100 dark:border-zinc-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 mb-4 lg:mb-6">Insights IA</h3>
                    <div class="space-y-4">
                        <!-- Insight 1 -->
                        <div class="bg-gradient-to-r from-primary-50 to-primary-100 dark:from-primary-900/20 dark:to-primary-800/20 rounded-lg p-3 lg:p-4 border-l-4 border-primary-500">
                            <div class="flex items-start">
                                <span class="material-icons text-primary-600 dark:text-primary-400 mr-3">lightbulb</span>
                                <div>
                                    <h4 class="text-sm font-medium text-primary-900 dark:text-primary-100">Dica de Economia</h4>
                                    <p class="text-sm text-primary-700 dark:text-primary-200 mt-1">Você gastou 23% a mais em alimentação este mês. Considere cozinhar mais em casa.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Insight 2 -->
                        <div class="bg-gradient-to-r from-warning-50 to-warning-100 dark:from-warning-900/20 dark:to-warning-800/20 rounded-lg p-3 lg:p-4 border-l-4 border-warning-500">
                            <div class="flex items-start">
                                <span class="material-icons text-warning-600 dark:text-warning-400 mr-3">warning</span>
                                <div>
                                    <h4 class="text-sm font-medium text-warning-900 dark:text-warning-100">Meta em Risco</h4>
                                    <p class="text-sm text-warning-700 dark:text-warning-200 mt-1">Você já usou 78% da sua meta de gastos para este mês.</p>
                                </div>
                            </div>
                        </div>

                        <!-- Insight 3 -->
                        <div class="bg-gradient-to-r from-success-50 to-success-100 dark:from-success-900/20 dark:to-success-800/20 rounded-lg p-3 lg:p-4 border-l-4 border-success-500">
                            <div class="flex items-start">
                                <span class="material-icons text-success-600 dark:text-success-400 mr-3">check_circle</span>
                                <div>
                                    <h4 class="text-sm font-medium text-success-900 dark:text-success-100">Parabéns!</h4>
                                    <p class="text-sm text-success-700 dark:text-success-200 mt-1">Você economizou MZN 2.500 comparado ao mês passado.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions and Goals -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 lg:gap-6">
                <!-- Transactions List -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700">
                    <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Transações Recentes</h3>
                            <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">Ver todas</a>
                        </div>
                    </div>
                    <div class="divide-y divide-gray-200 dark:divide-zinc-700">
                        <!-- Transaction 1 -->
                        <div class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-danger-100 dark:bg-danger-900/30 rounded-lg flex items-center justify-center">
                                    <span class="material-icons text-danger-600 dark:text-danger-400">shopping_cart</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Compras no Supermercado</p>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">Alimentação • Hoje, 14:30</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-danger-600 dark:text-danger-400">-MZN 1.250,00</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-zinc-200 mt-1">
                                        Confirmada
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction 2 -->
                        <div class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-success-100 dark:bg-success-900/30 rounded-lg flex items-center justify-center">
                                    <span class="material-icons text-success-600 dark:text-success-400">work</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Salário</p>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">Trabalho • Ontem, 09:00</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-success-600 dark:text-success-400">+MZN 35.000,00</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 dark:bg-zinc-700 text-gray-800 dark:text-zinc-200 mt-1">
                                        Confirmada
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- Transaction 3 -->
                        <div class="p-4 lg:p-6 hover:bg-gray-50 dark:hover:bg-zinc-700/50">
                            <div class="flex items-center space-x-4">
                                <div class="w-10 h-10 bg-warning-100 dark:bg-warning-900/30 rounded-lg flex items-center justify-center">
                                    <span class="material-icons text-warning-600 dark:text-warning-400">local_gas_station</span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Combustível</p>
                                    <p class="text-sm text-gray-500 dark:text-zinc-400">Transporte • 2 dias atrás</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-sm font-medium text-danger-600 dark:text-danger-400">-MZN 850,00</p>
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 dark:bg-yellow-900/30 text-yellow-800 dark:text-yellow-200 mt-1">
                                        Pendente
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Goals Progress -->
                <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-gray-100 dark:border-zinc-700">
                    <div class="p-4 lg:p-6 border-b border-gray-200 dark:border-zinc-700">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100">Progresso das Metas</h3>
                            <a href="#" class="text-primary-600 dark:text-primary-400 hover:text-primary-700 dark:hover:text-primary-300 text-sm font-medium">Gerenciar</a>
                        </div>
                    </div>
                    <div class="p-4 lg:p-6 space-y-6">
                        <!-- Goal 1 -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Meta de Economia</p>
                                <p class="text-sm text-gray-500 dark:text-zinc-400">MZN 8.500 / MZN 10.000</p>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-3">
                                <div class="bg-gradient-to-r from-success-500 to-success-600 h-3 rounded-full" style="width: 85%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">85% concluída</p>
                        </div>

                        <!-- Goal 2 -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Limite Alimentação</p>
                                <p class="text-sm text-gray-500 dark:text-zinc-400">MZN 3.900 / MZN 5.000</p>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-3">
                                <div class="bg-gradient-to-r from-warning-500 to-warning-600 h-3 rounded-full" style="width: 78%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">78% utilizada</p>
                        </div>

                        <!-- Goal 3 -->
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">Fundo de Emergência</p>
                                <p class="text-sm text-gray-500 dark:text-zinc-400">MZN 2.200 / MZN 15.000</p>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-zinc-600 rounded-full h-3">
                                <div class="bg-gradient-to-r from-primary-500 to-primary-600 h-3 rounded-full" style="width: 15%"></div>
                            </div>
                            <p class="text-xs text-gray-500 dark:text-zinc-400 mt-2">15% concluída</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Add Transaction Button -->
            <div class="fixed bottom-6 right-6">
                <button onclick="openModal()" class="w-12 h-12 lg:w-14 lg:h-14 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full shadow-lg flex items-center justify-center text-white hover:shadow-xl focus:outline-none">
                    <span class="material-icons text-lg lg:text-xl">add</span>
                </button>
            </div>
        </main>
    </div>

    <!-- Add Transaction Modal -->
    <div id="transactionModal" class="fixed inset-0 z-50 hidden bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full">
        <div class="relative top-20 mx-auto p-5 border w-80 lg:w-96 shadow-lg rounded-xl bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700">
            <div class="mt-3">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-zinc-100 text-center">Nova Transação</h3>
                <div class="mt-6 space-y-4">
                    <!-- Type Selection -->
                    <div class="grid grid-cols-2 gap-3">
                        <button class="px-4 py-3 bg-success-50 dark:bg-success-900/20 text-success-700 dark:text-success-300 border-2 border-success-200 dark:border-success-700 rounded-lg font-medium hover:bg-success-100 dark:hover:bg-success-900/30">
                            <span class="material-icons mr-2">add_circle</span>Receita
                        </button>
                        <button class="px-4 py-3 bg-gray-50 dark:bg-zinc-700 text-gray-700 dark:text-zinc-300 border-2 border-gray-200 dark:border-zinc-600 rounded-lg font-medium hover:bg-gray-100 dark:hover:bg-zinc-600">
                            <span class="material-icons mr-2">remove_circle</span>Gasto
                        </button>
                    </div>

                    <!-- Amount -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Valor</label>
                        <input type="number" class="w-full border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:border-primary-500" placeholder="0,00">
                    </div>

                    <!-- Category -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Categoria</label>
                        <select class="w-full border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:border-primary-500">
                            <option>Selecione uma categoria</option>
                            <option>Alimentação</option>
                            <option>Transporte</option>
                            <option>Saúde</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-zinc-300 mb-2">Descrição</label>
                        <input type="text" class="w-full border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 focus:outline-none focus:border-primary-500" placeholder="Descrição da transação">
                    </div>
                </div>

                <div class="flex justify-end space-x-3 mt-6">
                    <button onclick="closeModal()" class="px-4 py-2 bg-gray-300 dark:bg-zinc-600 text-gray-700 dark:text-zinc-300 rounded-lg hover:bg-gray-400 dark:hover:bg-zinc-500">
                        Cancelar
                    </button>
                    <button class="px-4 py-2 bg-primary-600 text-white rounded-lg hover:bg-primary-700">
                        Salvar
                    </button>
                </div>
            </div>
        </div>
    </div>
  <button onclick="showSuccess('Sucesso!', 'Transação salva com sucesso')" 
            class="bg-green-600 text-white px-4 py-2 rounded-lg">
        Teste Sucesso
    </button>
    <button onclick="showError('Erro!', 'Transação salva com sucesso')" 
            class="bg-red-600 text-white px-4 py-2 rounded-lg">
        Teste Sucesso
    </button>
    
        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
     <script>
        // Chart initialization
        
        const ctx = document.getElementById('spendingChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Alimentação', 'Transporte', 'Saúde', 'Lazer', 'Outros'],
                datasets: [{
                    data: [3900, 1200, 800, 950, 1300],
                    backgroundColor: [
                        '#EF4444',
                        '#F59E0B',
                        '#10B981',
                        '#6366F1',
                        '#8B5CF6'
                    ],
                    borderWidth: 0,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            color: document.documentElement.classList.contains('dark') ? '#e4e4e7' : '#374151'
                        }
                    }
                }
            }
        });

        // Modal functions
        function openModal() {
            document.getElementById('transactionModal').classList.remove('hidden');
        }

        function closeModal() {
            document.getElementById('transactionModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('transactionModal').addEventListener('click', function (e) {
            if (e.target === this) {
                closeModal();
            }
        });


    </script>
</x-layouts.perfic-layout>