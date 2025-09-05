{{-- resources/views/components/toast-notifications.blade.php --}}

<!-- Toast Container - Versão de Teste Simples -->
<div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm"></div>

<script>
// Versão pura JavaScript para teste
window.showToast = function(data) {
    console.log('Criando toast:', data);
    
    if (typeof data === 'string') {
        data = { title: data };
    }
    
    const container = document.getElementById('toast-container');
    if (!container) {
        console.error('Container não encontrado!');
        return;
    }
    
    // Criar elemento do toast
    const toast = document.createElement('div');
    toast.className = 'w-full shadow-lg rounded-lg pointer-events-auto border bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700 transform transition-all duration-300 translate-y-2 opacity-0';
    
    // Cores baseadas no tipo
    let bgClass = 'bg-white dark:bg-zinc-800';
    let borderClass = 'border-gray-200 dark:border-zinc-700';
    let iconColor = 'text-gray-400';
    let icon = 'info';
    
    switch(data.type) {
        case 'success':
            bgClass = 'bg-green-50 dark:bg-green-900/20';
            borderClass = 'border-green-200 dark:border-green-800';
            iconColor = 'text-green-500';
            icon = 'check_circle';
            break;
        case 'error':
            bgClass = 'bg-red-50 dark:bg-red-900/20';
            borderClass = 'border-red-200 dark:border-red-800';
            iconColor = 'text-red-500';
            icon = 'error';
            break;
        case 'warning':
            bgClass = 'bg-yellow-50 dark:bg-yellow-900/20';
            borderClass = 'border-yellow-200 dark:border-yellow-800';
            iconColor = 'text-yellow-500';
            icon = 'warning';
            break;
    }
    
    toast.className = toast.className.replace('bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700', `${bgClass} ${borderClass}`);
    
    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="material-icons text-xl ${iconColor}">${icon}</span>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-100">${data.title || 'Notificação'}</p>
                    ${data.message ? `<p class="mt-1 text-sm text-gray-500 dark:text-zinc-400">${data.message}</p>` : ''}
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button onclick="this.closest('[id^=toast-]').remove()" class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500">
                        <span class="material-icons text-lg">close</span>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // ID único para o toast
    const toastId = 'toast-' + Date.now();
    toast.id = toastId;
    
    // Adicionar ao container
    container.appendChild(toast);
    console.log('Toast adicionado ao DOM:', toastId);
    
    // Animar entrada
    setTimeout(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
        console.log('Animação de entrada aplicada');
    }, 10);
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        if (document.getElementById(toastId)) {
            toast.classList.add('translate-y-2', 'opacity-0');
            setTimeout(() => {
                if (toast.parentNode) {
                    toast.remove();
                    console.log('Toast removido automaticamente');
                }
            }, 300);
        }
    }, data.duration || 5000);
};

// Funções helper
window.showSuccess = function(title, message = '') {
    console.log('showSuccess chamado:', title, message);
    window.showToast({ type: 'success', title, message });
};

window.showError = function(title, message = '') {
    console.log('showError chamado:', title, message);
    window.showToast({ type: 'error', title, message });
};

window.showWarning = function(title, message = '') {
    console.log('showWarning chamado:', title, message);
    window.showToast({ type: 'warning', title, message });
};

window.showInfo = function(title, message = '') {
    console.log('showInfo chamado:', title, message);
    window.showToast({ type: 'info', title, message });
};

// Teste automático
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM carregado, aguardando 2 segundos...');
    setTimeout(() => {
        console.log('Executando teste automático');
        showSuccess('Sistema', 'Toast JavaScript puro funcionando!');
    }, 2000);
});

console.log('Script de toast carregado (JavaScript puro)');
</script>

<!-- Botões de teste -->
<div class="fixed bottom-4 left-4 space-y-2 z-[9999]">
    <button onclick="showSuccess('Sucesso!', 'Toast funcionou perfeitamente')" 
            class="block bg-green-600 text-white px-4 py-2 rounded-lg text-sm shadow-lg hover:bg-green-700">
        ✅ Teste Sucesso
    </button>
    
    <button onclick="showError('Erro!', 'Algo deu errado aqui')" 
            class="block bg-red-600 text-white px-4 py-2 rounded-lg text-sm shadow-lg hover:bg-red-700">
        ❌ Teste Erro
    </button>
    
    <button onclick="showWarning('Aviso!', 'Cuidado com isso')" 
            class="block bg-yellow-600 text-white px-4 py-2 rounded-lg text-sm shadow-lg hover:bg-yellow-700">
        ⚠️ Teste Aviso
    </button>
    
    <button onclick="showInfo('Info', 'Informação importante')" 
            class="block bg-blue-600 text-white px-4 py-2 rounded-lg text-sm shadow-lg hover:bg-blue-700">
        ℹ️ Teste Info
    </button>
</div>