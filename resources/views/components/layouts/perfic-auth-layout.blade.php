<!DOCTYPE html>
<html lang="pt-BR" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfic - Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        'inter': ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                            800: '#3730a3',
                            900: '#312e81',
                        }
                    },
                    backgroundImage: {
                        'gradient-bg': 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
                        'gradient-dark': 'linear-gradient(135deg, #1e293b 0%, #0f172a 100%)'
                    }
                }
            }
        }

        function toggleTheme() {
            const html = document.documentElement;
            const isDark = html.classList.contains('dark');
            const themeIcon = document.getElementById('themeIcon');

            if (isDark) {
                html.classList.remove('dark');
                localStorage.setItem('theme', 'light');
                themeIcon.textContent = 'dark_mode';
            } else {
                html.classList.add('dark');
                localStorage.setItem('theme', 'dark');
                themeIcon.textContent = 'light_mode';
            }
        }

        function initTheme() {
            const savedTheme = localStorage.getItem('theme');
            const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
            const themeIcon = document.getElementById('themeIcon');

            if (savedTheme === 'dark' || (!savedTheme && prefersDark)) {
                document.documentElement.classList.add('dark');
                if (themeIcon) themeIcon.textContent = 'light_mode';
            } else {
                if (themeIcon) themeIcon.textContent = 'dark_mode';
            }
        }

        function toggleForm() {
            const loginForm = document.getElementById('loginForm');
            const registerForm = document.getElementById('registerForm');
            const formTitle = document.getElementById('formTitle');
            const formSubtitle = document.getElementById('formSubtitle');
            const toggleText = document.getElementById('toggleText');
            const toggleLink = document.getElementById('toggleLink');
            
            if (loginForm.classList.contains('hidden')) {
                loginForm.classList.remove('hidden');
                registerForm.classList.add('hidden');
                formTitle.textContent = 'Sign in';
                formSubtitle.textContent = 'Acesse sua conta financeira';
                toggleText.textContent = "Don't have an account?";
                toggleLink.textContent = 'Sign up';
            } else {
                loginForm.classList.add('hidden');
                registerForm.classList.remove('hidden');
                formTitle.textContent = 'Sign up';
                formSubtitle.textContent = 'Crie sua conta e comece a controlar suas finanças';
                toggleText.textContent = 'Already have an account?';
                toggleLink.textContent = 'Sign in';
            }
        }

        document.addEventListener('DOMContentLoaded', initTheme);
    </script>
</head>
<body class="font-inter bg-gradient-bg dark:bg-gradient-dark min-h-screen relative overflow-hidden">
    
    <!-- Background com formas flutuantes -->
    <div class="absolute inset-0 overflow-hidden">
        <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-white/5 rounded-full blur-3xl"></div>
        <div class="absolute top-3/4 right-1/4 w-80 h-80 bg-purple-400/10 rounded-full blur-3xl"></div>
        <div class="absolute bottom-1/4 left-1/3 w-64 h-64 bg-blue-400/10 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 right-1/3 w-72 h-72 bg-indigo-400/8 rounded-full blur-3xl"></div>
    </div>

    <!-- Header flutuante MAIS LARGO -->
    <nav class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 bg-white/10 dark:bg-black/10 backdrop-blur-md border border-white/20 dark:border-white/10 rounded-2xl px-8 py-4 w-full max-w-6xl mx-4">
        <div class="flex items-center justify-between">
            <!-- Logo -->
            <div class="flex items-center space-x-3">
                <div class="w-8 h-8 bg-white/20 dark:bg-white/10 backdrop-blur-md rounded-lg flex items-center justify-center border border-white/30">
                    <span class="material-icons text-white text-lg">trending_up</span>
                </div>
                <h1 class="text-xl font-semibold text-white">Perfic</h1>
            </div>

            <!-- Navigation Links (só básico) -->
            <div class="hidden md:flex items-center space-x-6">
                <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Dashboard</a>
                <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Profile</a>
                <a href="#" class="text-white/80 hover:text-white text-sm font-medium transition-colors">Sign Up</a>
                <button onclick="toggleTheme()" class="p-2 text-white/80 hover:text-white focus:outline-none transition-colors">
                    <span id="themeIcon" class="material-icons text-lg">dark_mode</span>
                </button>
            </div>

            <!-- Mobile apenas tema -->
            <div class="md:hidden">
                <button onclick="toggleTheme()" class="p-2 text-white/80 hover:text-white focus:outline-none transition-colors">
                    <span class="material-icons text-lg">dark_mode</span>
                </button>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="min-h-screen flex items-center justify-center p-4 pt-24">
       {{ $slot }}
    </div>
</body>
</html>