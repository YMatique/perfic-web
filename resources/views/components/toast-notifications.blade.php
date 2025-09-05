{{-- resources/views/components/toast-notifications.blade.php --}}

<div id="toast-container" class="fixed top-4 right-4 z-[9999] space-y-2 max-w-sm"></div>

@once
<script>
window.showToast = function(data) {
    if (typeof data === 'string') {
        data = { title: data };
    }
    
    const container = document.getElementById('toast-container');
    if (!container) return;
    
    const toast = document.createElement('div');
    toast.className = 'w-full shadow-lg rounded-lg pointer-events-auto border transform transition-all duration-300 translate-y-2 opacity-0';
    
    let bgClass = 'bg-white dark:bg-zinc-800';
    let borderClass = 'border-gray-200 dark:border-zinc-700';
    let iconColor = 'text-gray-400 dark:text-zinc-400';
    let titleColor = 'text-gray-900 dark:text-zinc-100';
    let messageColor = 'text-gray-500 dark:text-zinc-400';
    let icon = 'info';
    
    switch(data.type) {
        case 'success':
            bgClass = 'bg-green-50 dark:bg-green-900/95';
            borderClass = 'border-green-200 dark:border-green-800';
            iconColor = 'text-green-500 dark:text-green-400';
            titleColor = 'text-green-900 dark:text-green-100';
            messageColor = 'text-green-700 dark:text-green-200';
            icon = 'check_circle';
            break;
        case 'error':
            bgClass = 'bg-red-50 dark:bg-red-900/95';
            borderClass = 'border-red-200 dark:border-red-800';
            iconColor = 'text-red-500 dark:text-red-400';
            titleColor = 'text-red-900 dark:text-red-100';
            messageColor = 'text-red-700 dark:text-red-200';
            icon = 'error';
            break;
        case 'warning':
            bgClass = 'bg-yellow-50 dark:bg-yellow-900/95';
            borderClass = 'border-yellow-200 dark:border-yellow-800';
            iconColor = 'text-yellow-500 dark:text-yellow-400';
            titleColor = 'text-yellow-900 dark:text-yellow-100';
            messageColor = 'text-yellow-700 dark:text-yellow-200';
            icon = 'warning';
            break;
    }
    
    toast.className += ` ${bgClass} ${borderClass}`;
    
    const toastId = 'toast-' + Date.now() + Math.random().toString(36).substr(2, 9);
    toast.id = toastId;
    
    toast.innerHTML = `
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <span class="material-icons text-xl ${iconColor}">${icon}</span>
                </div>
                <div class="ml-3 flex-1">
                    <p class="text-sm font-medium ${titleColor}">${data.title || 'Notificação'}</p>
                    ${data.message ? `<p class="mt-1 text-sm ${messageColor}">${data.message}</p>` : ''}
                </div>
                <div class="ml-4 flex-shrink-0">
                    <button onclick="this.closest('[id^=toast-]').remove()" 
                            class="inline-flex rounded-md p-1.5 text-gray-400 hover:text-gray-500 dark:text-zinc-400 dark:hover:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-gray-500 transition-colors">
                        <span class="material-icons text-lg">close</span>
                    </button>
                </div>
            </div>
        </div>
    `;
    
    container.appendChild(toast);
    
    setTimeout(() => {
        toast.classList.remove('translate-y-2', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    }, 10);
    
    const duration = data.duration || 5000;
    if (duration > 0) {
        setTimeout(() => {
            if (document.getElementById(toastId)) {
                toast.classList.add('translate-y-2', 'opacity-0');
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.remove();
                    }
                }, 300);
            }
        }, duration);
    }
};

window.showSuccess = function(title, message = '') {
    window.showToast({ type: 'success', title, message });
};

window.showError = function(title, message = '') {
    window.showToast({ type: 'error', title, message });
};

window.showWarning = function(title, message = '') {
    window.showToast({ type: 'warning', title, message });
};

window.showInfo = function(title, message = '') {
    window.showToast({ type: 'info', title, message });
};


// Listener para eventos Livewire
document.addEventListener('livewire:init', () => {
    Livewire.on('toast', (data) => {
        const toastData = Array.isArray(data) ? data[0] : data;
        showToast(toastData);
    });
});
</script>

<style>
@media (max-width: 640px) {
    #toast-container {
        left: 1rem;
        right: 1rem;
        top: 1rem;
        max-width: none;
    }
}
</style>
@endonce
