<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="{ darkMode: false }" x-init="darkMode = localStorage.getItem('theme') === 'dark' || (!localStorage.getItem('theme') && window.matchMedia('(prefers-color-scheme: dark)').matches)"
    :class="{ 'dark': darkMode }">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Perfic - Controle Financeiro Pessoal' }}</title>

    <!-- Favicon -->
    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.svg" type="image/svg+xml">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:300,400,500,600,700" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
</head>

<body class="font-inter bg-gray-50 dark:bg-zinc-900 min-h-screen text-gray-900 dark:text-zinc-100 antialiased"
    x-data="appLayout()" x-init="init()">

    <!-- Mobile Sidebar Overlay -->
    <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-linear duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition-opacity ease-linear duration-300" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
        @click="mobileMenuOpen = false" style="display: none;">
    </div>

    <!-- Desktop Sidebar -->
    <div id="sidebar" :class="{ 'w-64': !sidebarCollapsed, 'w-16': sidebarCollapsed }"
        class="fixed inset-y-0 left-0 z-50 bg-white dark:bg-zinc-800 shadow-lg border-r border-gray-200 dark:border-zinc-700 hidden lg:block transition-all duration-300 ease-in-out">

        <!-- Logo -->
        <div class="flex items-center h-16 px-4 border-b border-gray-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3 min-w-0">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center flex-shrink-0">
                    <span class="material-icons text-white text-lg">trending_up</span>
                </div>
                <h1 x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300"
                    x-transition:enter-start="opacity-0 -translate-x-2"
                    x-transition:enter-end="opacity-100 translate-x-0"
                    x-transition:leave="transition ease-in-out duration-300"
                    x-transition:leave-start="opacity-100 translate-x-0"
                    x-transition:leave-end="opacity-0 -translate-x-2"
                    class="text-lg font-semibold text-gray-800 dark:text-zinc-100 truncate">
                    Perfic
                </h1>
            </div>
            <!-- Collapse Button -->
            <button @click="toggleSidebar()" :class="{ 'ml-auto': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }"
                class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                <span class="material-icons text-gray-500 dark:text-zinc-400 text-lg">
                    <span x-show="!sidebarCollapsed">menu_open</span>
                    <span x-show="sidebarCollapsed">menu</span>
                </span>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="mt-2 px-3 space-y-1">
            <!-- Dashboard -->
            <a href="{{ route('dashboard') }}" wire:navigate
                class="@if (request()->routeIs('dashboard')) bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border-r-2 border-primary-500 @else text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 @endif group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">dashboard</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Dashboard
                </span>
            </a>

            <!-- Transações -->
            <!-- Transações -->
            <a href="{{ route('transactions') }}" wire:navigate
                class="@if (request()->routeIs('transactions')) bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border-r-2 border-primary-500 @else text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 @endif group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">swap_horiz</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Transações
                </span>
            </a>
            <!-- Transações Recorrentes -->
            <a href="{{ route('recurring') }}" wire:navigate
                class="@if (request()->routeIs('recurring')) bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border-r-2 border-primary-500 @else text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 @endif group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">autorenew</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Recorrentes
                </span>
            </a>
            <!-- Relatórios -->
            <a href="#"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">pie_chart</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Relatórios
                </span>
            </a>

            <!-- Categorias -->
            <a href="{{ route('categories') }}" wire:navigate
                class="@if (request()->routeIs('categories')) bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border-r-2 border-primary-500 @else text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 @endif group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">category</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Categorias
                </span>
            </a>


            <!-- Metas -->
            <!-- Metas -->
            <a href="{{ route('goals') }}" wire:navigate
                class="@if (request()->routeIs('goals')) bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 border-r-2 border-primary-500 @else text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 @endif group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">flag</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Metas
                </span>
            </a>

            <!-- Insights IA -->
            <a href="#"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-3 py-2.5 text-sm font-medium rounded-lg transition-colors">
                <span class="material-icons flex-shrink-0"
                    :class="{ 'mr-3': !sidebarCollapsed, 'mx-auto': sidebarCollapsed }">psychology</span>
                <span x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="truncate">
                    Insights IA
                </span>
            </a>
        </nav>

        <!-- User Profile -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3" :class="{ 'justify-center': sidebarCollapsed }">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-medium text-sm">{{ auth()->user()->initials() }}</span>
                </div>
                <div x-show="!sidebarCollapsed" x-transition:enter="transition ease-in-out duration-300 delay-75"
                    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                    x-transition:leave="transition ease-in-out duration-200" x-transition:leave-start="opacity-100"
                    x-transition:leave-end="opacity-0" class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-100 truncate">{{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Sidebar -->
    <div x-show="mobileMenuOpen" x-transition:enter="transform transition ease-in-out duration-300"
        x-transition:enter-start="-translate-x-full" x-transition:enter-end="translate-x-0"
        x-transition:leave="transform transition ease-in-out duration-300" x-transition:leave-start="translate-x-0"
        x-transition:leave-end="-translate-x-full"
        class="fixed inset-y-0 left-0 z-50 w-64 bg-white dark:bg-zinc-800 shadow-lg lg:hidden" style="display: none;">

        <!-- Logo -->
        <div class="flex items-center justify-between h-16 px-4 border-b border-gray-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3">
                <div
                    class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-lg flex items-center justify-center">
                    <span class="material-icons text-white text-lg">trending_up</span>
                </div>
                <h1 class="text-lg font-semibold text-gray-800 dark:text-zinc-100">Perfic</h1>
            </div>
            <button @click="mobileMenuOpen = false" class="p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700">
                <span class="material-icons text-gray-500 dark:text-zinc-400">close</span>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <nav class="mt-4 px-4 space-y-2">
            <a href="{{ route('dashboard') }}" wire:navigate @click="mobileMenuOpen = false"
                class="@if (request()->routeIs('dashboard')) bg-primary-50 dark:bg-primary-900/20 text-primary-700 dark:text-primary-300 @else text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 @endif group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <span class="material-icons mr-3">dashboard</span>
                Dashboard
            </a>
            <a href="#" @click="mobileMenuOpen = false"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <span class="material-icons mr-3">swap_horiz</span>
                Transações
            </a>
            <a href="#" @click="mobileMenuOpen = false"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <span class="material-icons mr-3">pie_chart</span>
                Relatórios
            </a>
            <a href="#" @click="mobileMenuOpen = false"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <span class="material-icons mr-3">category</span>
                Categorias
            </a>
            <a href="#" @click="mobileMenuOpen = false"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <span class="material-icons mr-3">flag</span>
                Metas
            </a>
            <a href="#" @click="mobileMenuOpen = false"
                class="text-gray-600 dark:text-zinc-400 hover:bg-gray-50 dark:hover:bg-zinc-700 hover:text-gray-900 dark:hover:text-zinc-100 group flex items-center px-4 py-3 text-sm font-medium rounded-lg">
                <span class="material-icons mr-3">psychology</span>
                Insights IA
            </a>
        </nav>

        <!-- Mobile User Profile -->
        <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 dark:border-zinc-700">
            <div class="flex items-center space-x-3">
                <div
                    class="w-10 h-10 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center">
                    <span class="text-white font-medium text-sm">{{ auth()->user()->initials() }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-gray-900 dark:text-zinc-100 truncate">
                        {{ auth()->user()->name }}
                    </p>
                    <p class="text-xs text-gray-500 dark:text-zinc-400 truncate">{{ auth()->user()->email }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div :class="{ 'lg:ml-64': !sidebarCollapsed, 'lg:ml-16': sidebarCollapsed }"
        class="transition-all duration-300 ease-in-out">

        <!-- Top Navigation Bar -->
        <nav
            class="bg-white dark:bg-zinc-800 shadow-sm border-b border-gray-200 dark:border-zinc-700 lg:border-b-0 lg:shadow-none">
            <div class="px-4 lg:px-6 py-3 lg:py-4">
                <div class="flex items-center justify-between">

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = true"
                        class="lg:hidden p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                        <span class="material-icons text-gray-500 dark:text-zinc-400">menu</span>
                    </button>

                    <!-- Breadcrumb -->
                    <nav aria-label="breadcrumb" class="hidden lg:block">
                        <ol class="flex items-center space-x-2 text-sm">
                            <li>
                                <a href="#"
                                    class="text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200">Páginas</a>
                            </li>
                            <li class="text-gray-400 dark:text-zinc-500">/</li>
                            <li class="text-gray-900 dark:text-zinc-100 font-medium">{{ $pageTitle ?? 'Dashboard' }}
                            </li>
                        </ol>
                    </nav>

                    <!-- Mobile Page Title -->
                    <h1 class="lg:hidden text-lg font-semibold text-gray-900 dark:text-zinc-100">
                        {{ $pageTitle ?? 'Dashboard' }}</h1>

                    <!-- Right side actions -->
                    <div class="flex items-center space-x-2 lg:space-x-4">
                        <!-- Search (Desktop only) -->
                        <div class="hidden lg:block relative">
                            <input type="text" placeholder="Buscar..."
                                class="w-64 px-4 py-2 text-sm border border-gray-300 dark:border-zinc-600 rounded-lg bg-white dark:bg-zinc-700 text-gray-900 dark:text-zinc-100 placeholder-gray-500 dark:placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-primary-500 focus:border-transparent transition-colors">
                            <span
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 material-icons text-gray-400 text-lg">search</span>
                        </div>

                        <!-- Theme Toggle -->
                        <button @click="toggleTheme()"
                            class="p-2 text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                            <span class="material-icons text-lg"
                                x-text="darkMode ? 'light_mode' : 'dark_mode'"></span>
                        </button>

                        <!-- Notifications -->
                        <div class="relative">
                            <button
                                class="p-2 text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                                <span class="material-icons text-lg">notifications</span>
                                <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                            </button>
                        </div>

                        <!-- Settings -->
                        <button
                            class="hidden lg:block p-2 text-gray-500 dark:text-zinc-400 hover:text-gray-700 dark:hover:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700 rounded-lg transition-colors">
                            <span class="material-icons text-lg">settings</span>
                        </button>

                        <!-- Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open"
                                class="flex items-center space-x-2 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-zinc-700 transition-colors">
                                <div
                                    class="w-8 h-8 bg-gradient-to-br from-primary-500 to-primary-700 rounded-full flex items-center justify-center">
                                    <span
                                        class="text-white font-medium text-xs">{{ auth()->user()->initials() }}</span>
                                </div>
                                <span
                                    class="hidden lg:block material-icons text-gray-500 dark:text-zinc-400 text-sm">keyboard_arrow_down</span>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" @click.away="open = false"
                                x-transition:enter="transition ease-out duration-100"
                                x-transition:enter-start="transform opacity-0 scale-95"
                                x-transition:enter-end="transform opacity-100 scale-100"
                                x-transition:leave="transition ease-in duration-75"
                                x-transition:leave-start="transform opacity-100 scale-100"
                                x-transition:leave-end="transform opacity-0 scale-95"
                                class="absolute right-0 mt-2 w-48 bg-white dark:bg-zinc-800 rounded-lg shadow-lg border border-gray-200 dark:border-zinc-700 z-50"
                                style="display: none;">
                                <div class="py-1">
                                    <a href="{{ route('settings.profile') }}" wire:navigate
                                        class="flex items-center px-4 py-2 text-sm text-gray-700 dark:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700">
                                        <span class="material-icons mr-3 text-lg">settings</span>
                                        Configurações
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                                        @csrf
                                        <button type="submit"
                                            class="flex items-center w-full px-4 py-2 text-sm text-gray-700 dark:text-zinc-200 hover:bg-gray-100 dark:hover:bg-zinc-700 text-left">
                                            <span class="material-icons mr-3 text-lg">logout</span>
                                            Sair
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <main class="min-h-screen bg-gray-50 dark:bg-zinc-900">
            {{ $slot }}
        </main>
    </div>

    <!-- Floating Action Button (Mobile) -->
    <div class="fixed bottom-6 right-6 lg:hidden z-40">
        <button
            class="w-14 h-14 bg-gradient-to-r from-primary-500 to-primary-600 rounded-full shadow-lg flex items-center justify-center text-white hover:shadow-xl focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2 transition-all">
            <span class="material-icons text-xl">add</span>
        </button>
    </div>

    @fluxScripts
    @livewireScripts
    @yield('scripts')

    <script>
        function appLayout() {
            return {
                sidebarCollapsed: localStorage.getItem('sidebarCollapsed') === 'true',
                mobileMenuOpen: false,

                init() {
                    // Watch for changes and persist state
                    this.$watch('sidebarCollapsed', value => {
                        localStorage.setItem('sidebarCollapsed', value);
                    });
                },

                toggleSidebar() {
                    this.sidebarCollapsed = !this.sidebarCollapsed;
                },

                toggleTheme() {
                    this.darkMode = !this.darkMode;
                    localStorage.setItem('theme', this.darkMode ? 'dark' : 'light');
                }
            }
        }
    </script>

    <style>
        /* Custom scrollbar for sidebar */
        #sidebar {
            scrollbar-width: thin;
            scrollbar-color: rgba(156, 163, 175, 0.5) transparent;
        }

        #sidebar::-webkit-scrollbar {
            width: 6px;
        }

        #sidebar::-webkit-scrollbar-track {
            background: transparent;
        }

        #sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(156, 163, 175, 0.5);
            border-radius: 3px;
        }

        #sidebar::-webkit-scrollbar-thumb:hover {
            background-color: rgba(156, 163, 175, 0.7);
        }

        /* Smooth transitions for all interactive elements */
        * {
            transition-property: color, background-color, border-color, text-decoration-color, fill, stroke, opacity, box-shadow, transform, filter, backdrop-filter;
            transition-duration: 150ms;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
        }
    </style>
</body>

</html>
