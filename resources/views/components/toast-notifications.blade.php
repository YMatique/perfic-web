{{-- resources/views/components/toast-notifications.blade.php --}}

<div x-data="toastManager()" 
     @toast.window="addToast($event.detail)"
     class="fixed top-4 right-4 z-50 space-y-2 max-w-sm">
    
    <template x-for="toast in toasts" :key="toast.id">
        <div x-show="toast.visible"
             x-transition:enter="transform ease-out duration-300 transition"
             x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
             x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             :class="{
                'bg-white dark:bg-zinc-800 border-gray-200 dark:border-zinc-700': toast.type === 'info',
                'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800': toast.type === 'success',
                'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800': toast.type === 'error',
                'bg-yellow-50 dark:bg-yellow-900/20 border-yellow-200 dark:border-yellow-800': toast.type === 'warning'
             }"
             class="w-full shadow-lg rounded-lg pointer-events-auto border">
            
            <div class="p-4">
                <div class="flex items-start">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        <span class="material-icons text-xl"
                              :class="{
                                'text-gray-400 dark:text-zinc-400': toast.type === 'info',
                                'text-green-500 dark:text-green-400': toast.type === 'success',
                                'text-red-500 dark:text-red-400': toast.type === 'error',
                                'text-yellow-500 dark:text-yellow-400': toast.type === 'warning'
                              }"
                              x-text="getIcon(toast.type)">
                        </span>
                    </div>
                    
                    <!-- Content -->
                    <div class="ml-3 w-0 flex-1">
                        <p class="text-sm font-medium"
                           :class="{
                             'text-gray-900 dark:text-zinc-100': toast.type === 'info',
                             'text-green-900 dark:text-green-100': toast.type === 'success',
                             'text-red-900 dark:text-red-100': toast.type === 'error',
                             'text-yellow-900 dark:text-yellow-100': toast.type === 'warning'
                           }"
                           x-text="toast.title">
                        </p>
                        <p class="mt-1 text-sm"
                           :class="{
                             'text-gray-500 dark:text-zinc-400': toast.type === 'info',
                             'text-green-700 dark:text-green-200': toast.type === 'success',
                             'text-red-700 dark:text-red-200': toast.type === 'error',
                             'text-yellow-700 dark:text-yellow-200': toast.type === 'warning'
                           }"
                           x-show="toast.message"
                           x-text="toast.message">
                        </p>
                    </div>
                    
                    <!-- Close Button -->
                    <div class="ml-4 flex-shrink-0 flex">
                        <button @click="removeToast(toast.id)"
                                class="inline-flex rounded-md p-1.5 focus:outline-none focus:ring-2 focus:ring-offset-2 transition-colors"
                                :class="{
                                  'text-gray-400 hover:text-gray-500 dark:text-zinc-400 dark:hover:text-zinc-300 focus:ring-gray-500': toast.type === 'info',
                                  'text-green-400 hover:text-green-500 dark:text-green-400 dark:hover:text-green-300 focus:ring-green-500': toast.type === 'success',
                                  'text-red-400 hover:text-red-500 dark:text-red-400 dark:hover:text-red-300 focus:ring-red-500': toast.type === 'error',
                                  'text-yellow-400 hover:text-yellow-500 dark:text-yellow-400 dark:hover:text-yellow-300 focus:ring-yellow-500': toast.type === 'warning'
                                }">
                            <span class="material-icons text-lg">close</span>
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Progress Bar -->
            <div x-show="toast.duration > 0" 
                 class="h-1 bg-gray-200 dark:bg-zinc-600 rounded-b-lg overflow-hidden">
                <div class="h-full transition-all ease-linear"
                     :class="{
                       'bg-gray-400': toast.type === 'info',
                       'bg-green-500': toast.type === 'success',
                       'bg-red-500': toast.type === 'error',
                       'bg-yellow-500': toast.type === 'warning'
                     }"
                     :style="`width: ${toast.progress}%; transition-duration: ${toast.duration}ms;`">
                </div>
            </div>
        </div>
    </template>
</div>

@once
<script>
function toastManager() {
    return {
        toasts: [],
        nextId: 1,
        
        addToast(data) {
            const toast = {
                id: this.nextId++,
                type: data.type || 'info',
                title: data.title || 'Notificação',
                message: data.message || '',
                duration: data.duration || 5000,
                visible: false,
                progress: 100
            };
            
            this.toasts.push(toast);
            
            this.$nextTick(() => {
                toast.visible = true;
                
                if (toast.duration > 0) {
                    setTimeout(() => {
                        toast.progress = 0;
                    }, 100);
                    
                    setTimeout(() => {
                        this.removeToast(toast.id);
                    }, toast.duration);
                }
            });
        },
        
        removeToast(id) {
            const index = this.toasts.findIndex(toast => toast.id === id);
            if (index > -1) {
                this.toasts[index].visible = false;
                setTimeout(() => {
                    this.toasts.splice(index, 1);
                }, 300);
            }
        },
        
        getIcon(type) {
            const icons = {
                'success': 'check_circle',
                'error': 'error',
                'warning': 'warning',
                'info': 'info'
            };
            return icons[type] || 'info';
        }
    }
}

// Helper functions
window.showToast = function(data) {
    if (typeof data === 'string') {
        data = { message: data };
    }
    window.dispatchEvent(new CustomEvent('toast', { detail: data }));
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
</script>

<style>
@media (max-width: 640px) {
    .toast-container {
        left: 1rem;
        right: 1rem;
        top: 1rem;
    }
}
</style>
@endonce