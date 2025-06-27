<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="theme-color" content="#1a1a1a">
    
    <title>@yield('title', 'OneTap - Social Photo Roulette')</title>
    <meta name="description" content="@yield('description', 'Take one authentic photo per day and see what the world is doing at the same moment. No filters, just real moments.')">
    
    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <link rel="icon" type="image/png" href="/favicon.png">
    
    <!-- PWA Meta Tags -->
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="apple-mobile-web-app-title" content="OneTap">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">
    <link rel="manifest" href="/manifest.json">
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet">
    
    <!-- Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        :root {
            --primary: #ff6b6b;
            --primary-dark: #ff5252;
            --secondary: #4ecdc4;
            --accent: #ffe66d;
            --bg-dark: #0a0a0a;
            --bg-card: #1a1a1a;
            --text-primary: #ffffff;
            --text-secondary: #a0a0a0;
            --border: #333333;
            --success: #4ade80;
            --warning: #fbbf24;
            --error: #ef4444;
        }
        
        * {
            -webkit-tap-highlight-color: transparent;
        }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text-primary);
            overscroll-behavior: none;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        }
        
        .glass-effect {
            backdrop-filter: blur(20px);
            background: rgba(26, 26, 26, 0.8);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            border: none;
            border-radius: 16px;
            padding: 16px 24px;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255, 107, 107, 0.3);
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(255, 107, 107, 0.4);
        }
        
        .btn-primary:active {
            transform: translateY(0);
        }
        
        .pulse-animation {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .bounce-in {
            animation: bounceIn 0.6s ease-out;
        }
        
        @keyframes bounceIn {
            0% { transform: scale(0.3); opacity: 0; }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { transform: scale(1); opacity: 1; }
        }
        
        .slide-up {
            animation: slideUp 0.4s ease-out;
        }
        
        @keyframes slideUp {
            from { transform: translateY(100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        
        .notification-dot {
            position: relative;
        }
        
        .notification-dot::after {
            content: '';
            position: absolute;
            top: -2px;
            right: -2px;
            width: 8px;
            height: 8px;
            background: var(--error);
            border-radius: 50%;
            animation: pulse 2s infinite;
        }
        
        .streak-fire {
            background: linear-gradient(45deg, #ff6b6b, #ffa500);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 4px;
        }
        
        ::-webkit-scrollbar-track {
            background: var(--bg-card);
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary);
            border-radius: 2px;
        }
        
        /* Loading spinner */
        .spinner {
            width: 24px;
            height: 24px;
            border: 3px solid rgba(255, 255, 255, 0.1);
            border-top: 3px solid var(--primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        
        /* Mobile optimizations */
        @media (max-width: 768px) {
            .container {
                padding: 0 16px;
            }
            
            .btn-primary {
                width: 100%;
                padding: 18px 24px;
                font-size: 18px;
            }
        }
        
        /* Safe area for iPhone notch */
        @supports (padding: max(0px)) {
            .safe-top {
                padding-top: max(24px, env(safe-area-inset-top));
            }
            
            .safe-bottom {
                padding-bottom: max(24px, env(safe-area-inset-bottom));
            }
        }
    </style>
    
    @stack('styles')
</head>
<body class="antialiased">
    <!-- Loading Screen -->
    <div id="app-loader" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900">
        <div class="text-center">
            <div class="w-16 h-16 mx-auto mb-4 gradient-bg rounded-full flex items-center justify-center">
                <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold gradient-bg bg-clip-text text-transparent">OneTap</h1>
            <div class="spinner mt-4 mx-auto"></div>
        </div>
    </div>


    <!-- Main App Container -->
    <div id="app" class="min-h-screen flex flex-col" style="display: none;">
        <!-- Navigation Header -->




        @if(!in_array(Route::currentRouteName(), ['welcome', 'login', 'register']))
        <header class="safe-top sticky top-0 z-40 glass-effect">
            <div class="max-w-md mx-auto px-4 py-3">
                <div class="flex items-center justify-between">

                    <!-- Logo/Back -->
                    <div class="flex items-center space-x-3">
                        @if(request()->is('/') || request()->is('dashboard'))
                            <div class="w-8 h-8 gradient-bg rounded-lg flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                        @else
                            <button onclick="window.history.back()" class="w-8 h-8 rounded-lg bg-gray-800 flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                        @endif
                        
                        <div>
                            <h1 class="text-lg font-bold">@yield('header-title', 'OneTap')</h1>
                            @hasSection('header-subtitle')
                            <p class="text-sm text-gray-400">@yield('header-subtitle')</p>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        @auth
                            <!-- Streak Counter -->
                            <div class="flex items-center space-x-1 px-3 py-1 bg-gray-800 rounded-full">
                                <span class="text-lg">🔥</span>
                                <span class="text-sm font-semibold streak-fire">{{ auth()->user()->streak ?? 0 }}</span>
                            </div>
                            
                            <!-- Notifications -->
                            <button class="w-8 h-8 rounded-lg bg-gray-800 flex items-center justify-center notification-dot">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4"/>
                                </svg>
                            </button>
                            
                            <!-- Profile -->
                            <a href="" class="w-8 h-8 rounded-lg bg-gradient-to-br from-pink-500 to-purple-600 flex items-center justify-center">
                                <span class="text-sm font-bold text-white">{{ substr(auth()->user()->name, 0, 1) }}</span>
                            </a>
                        @endauth
                    </div>
                    
                </div>
            </div>
            @auth
                <div class="flex justify-end mb-4">
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-sm text-red-500 flex items-center space-x-1 hover:underline">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 11-6 0v-1m6-4V9a3 3 0 10-6 0v1" />
                            </svg>
                            <span>Logout</span>
                        </button>
                    </form>
                </div>
            @endauth
        </header>
        @endif

        <!-- Main Content -->
        <main class="flex-1 flex flex-col">
            @yield('content')
        </main>

        <!-- Bottom Navigation (for authenticated users) -->
        @auth
        @if(!in_array(Route::currentRouteName(), ['welcome', 'login', 'register']))
        <nav class="safe-bottom sticky bottom-0 z-40 glass-effect">
            <div class="max-w-md mx-auto px-4 py-2">
                <div class="flex items-center justify-around">
                    <a href="" class="nav-item ">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs mt-1">Home</span>
                    </a>
                    
                    <a href=" " class="nav-item  ">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-xs mt-1">Moments</span>
                    </a>
                    
                    <a href=" " class="nav-item-center">
                        <div class="w-14 h-14 gradient-bg rounded-full flex items-center justify-center shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </a>
                    
                    <a href=" " class="nav-item  ">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        <span class="text-xs mt-1">Ranks</span>
                    </a>
                    
                    <a href="{{ route('my.profile', ['user' => auth()->user()->username]) }}" class="nav-item">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                        </svg>
                        <span class="text-xs mt-1">Profile</span>
                    </a>

                </div>
            </div>
        </nav>
        @endif
        @endauth
    </div>

    <!-- Toast Notifications -->
    <div id="toast-container" class="fixed top-4 right-4 z-50 space-y-2"></div>

    <!-- Modal Container -->
    <div id="modal-container"></div>

    <!-- Scripts -->
    <script>
        // Hide loader and show app when DOM is ready
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(() => {
                document.getElementById('app-loader').style.display = 'none';
                document.getElementById('app').style.display = 'flex';
            }, 1000);
        });
        
        // Toast notification system
        window.showToast = function(message, type = 'info') {
            const toast = document.createElement('div');
            toast.className = `toast slide-up px-4 py-3 rounded-lg shadow-lg max-w-sm ${
                type === 'success' ? 'bg-green-600' : 
                type === 'error' ? 'bg-red-600' : 
                type === 'warning' ? 'bg-yellow-600' : 'bg-blue-600'
            } text-white`;
            toast.textContent = message;
            
            document.getElementById('toast-container').appendChild(toast);
            
            setTimeout(() => {
                toast.remove();
            }, 4000);
        };
        
        // Global error handler
        window.addEventListener('error', function(e) {
            console.error('Global error:', e.error);
        });
        
        // PWA install prompt
        let deferredPrompt;
        window.addEventListener('beforeinstallprompt', (e) => {
            deferredPrompt = e;
        });
        
        // Service worker registration
        if ('serviceWorker' in navigator) {
            window.addEventListener('load', () => {
                navigator.serviceWorker.register('/sw.js')
                    .then(registration => {
                        console.log('SW registered: ', registration);
                    })
                    .catch(registrationError => {
                        console.log('SW registration failed: ', registrationError);
                    });
            });
        }
    </script>
    
    @stack('scripts')
    
    <style>
        .nav-item {
            @apply flex flex-col items-center py-2 px-3 rounded-lg text-gray-400 transition-colors;
        }
        
        .nav-item.active {
            @apply text-white;
        }
        
        .nav-item-center {
            @apply flex items-center justify-center -mt-6;
        }
        
        .nav-item:hover {
            @apply text-white bg-gray-800;
        }
    </style>
</body>
</html>