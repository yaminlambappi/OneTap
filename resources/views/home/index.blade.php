@extends('layouts.base')

@section('title', 'Dashboard - OneTap')
@section('header-title', 'Today\'s Moment')
@section('header-subtitle', date('F j, Y'))

@section('content')
<div class="flex-1 bg-gray-900">
    <!-- Hero Section with Daily Photo Action -->
    <div class="relative overflow-hidden">
        <!-- Animated Background -->
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-20 h-20 bg-pink-500 rounded-full animate-pulse"></div>
            <div class="absolute top-32 right-16 w-12 h-12 bg-cyan-400 rounded-full animate-bounce" style="animation-delay: 0.5s"></div>
            <div class="absolute bottom-40 left-20 w-16 h-16 bg-yellow-400 rounded-full animate-pulse" style="animation-delay: 1s"></div>
            <div class="absolute bottom-20 right-8 w-8 h-8 bg-purple-500 rounded-full animate-bounce" style="animation-delay: 1.5s"></div>
        </div>

        <div class="relative px-6 py-8">
            <!-- Daily Status Card -->
            <div class="bg-gray-800 rounded-2xl p-6 mb-6 border border-gray-700">
                <div class="text-center">
                    @auth
                        @if(auth()->user()->hasPostedToday ?? false)
                            <!-- Already Posted Today -->
                            <div class="mb-4">
                                <div class="w-16 h-16 mx-auto bg-green-600 rounded-full flex items-center justify-center mb-3">
                                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                                    </svg>
                                </div>
                                <h2 class="text-xl font-bold text-white mb-2">Photo Shared! ✨</h2>
                                <p class="text-gray-400 text-sm">Come back tomorrow for your next moment</p>
                            </div>
                            
                            <!-- Countdown to Next Photo -->
                            <div class="bg-gray-700 rounded-xl p-4 mb-4">
                                <p class="text-gray-300 text-sm mb-2">Next photo in:</p>
                                <div id="countdown" class="text-2xl font-bold gradient-bg bg-clip-text text-transparent">
                                    <span id="hours">--</span>:<span id="minutes">--</span>:<span id="seconds">--</span>
                                </div>
                            </div>
                        @else
                            <!-- Ready to Take Photo -->
                            <div class="mb-6">
                                <div class="w-20 h-20 mx-auto gradient-bg rounded-full flex items-center justify-center mb-4 pulse-animation">
                                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    </svg>
                                </div>
                                <h2 class="text-2xl font-bold text-white mb-2">Ready to Capture?</h2>
                                <p class="text-gray-400 text-sm mb-6">Take your daily photo and see what 5 strangers are doing right now</p>
                                
                                <button class="btn-primary w-full" onclick="takePhoto()">
                                    📸 Take Today's Photo
                                </button>
                            </div>
                        @endif
                    @else
                        <!-- Not Authenticated -->
                        <div class="mb-6">
                            <div class="w-20 h-20 mx-auto gradient-bg rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 text-white" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                </svg>
                            </div>
                            <h2 class="text-2xl font-bold text-white mb-2">Welcome to OneTap</h2>
                            <p class="text-gray-400 text-sm mb-6">One photo per day. Real moments. Global connection.</p>
                            
                            <div class="space-y-3">
                                <a href="{{ route('login') }}" class="btn-primary w-full block text-center">
                                    🚀 Get Started
                                </a>
                                <a href="{{ route('register')}}" class="block text-center text-gray-400 text-sm">
                                    New here? Create account
                                </a>
                            </div>
                        </div>
                    @endauth
                </div>
            </div>

            @auth
            <!-- Today's Global Moments -->
            <div class="mb-6">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-white">🌍 Global Moments</h3>
                    <span class="text-xs text-gray-400 bg-gray-800 px-2 py-1 rounded-full">
                        {{ rand(1200, 5000) }} photos today
                    </span>
                </div>
                
                <!-- Photo Grid -->
                <div class="grid grid-cols-2 gap-3 mb-4">
                    @for($i = 0; $i < 4; $i++)
                    <div class="relative aspect-square bg-gray-800 rounded-xl overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-br from-pink-500/20 to-purple-600/20 flex items-center justify-center">
                            <div class="text-center">
                                <div class="w-12 h-12 mx-auto bg-gray-700 rounded-full flex items-center justify-center mb-2">
                                    <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                    </svg>
                                </div>
                                <p class="text-xs text-gray-400">{{ ['Tokyo', 'Paris', 'NYC', 'London'][rand(0,3)] }}</p>
                                <p class="text-xs text-gray-500">{{ rand(1, 60) }}m ago</p>
                            </div>
                        </div>
                        
                        <!-- Reaction overlay -->
                        <div class="absolute bottom-2 left-2 flex space-x-1">
                            <span class="text-xs">❤️</span>
                            <span class="text-xs text-gray-400">{{ rand(5, 99) }}</span>
                        </div>
                    </div>
                    @endfor
                </div>
                
                <a href="/moments" class="block text-center text-sm text-cyan-400 font-medium">
                    View all moments →
                </a>
            </div>

            <!-- Stats & Achievements -->
            <div class="grid grid-cols-2 gap-4 mb-6">
                <!-- Streak -->
                <div class="bg-gray-800 rounded-xl p-4 text-center border border-gray-700">
                    <div class="text-2xl mb-1">🔥</div>
                    <div class="text-xl font-bold streak-fire">{{ auth()->user()->streak ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Day Streak</div>
                </div>
                
                <!-- Photos Shared -->
                <div class="bg-gray-800 rounded-xl p-4 text-center border border-gray-700">
                    <div class="text-2xl mb-1">📸</div>
                    <div class="text-xl font-bold text-cyan-400">{{ auth()->user()->photos_count ?? 0 }}</div>
                    <div class="text-xs text-gray-400">Photos Shared</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="space-y-3">
                <a href="/leaderboard" class="flex items-center justify-between bg-gray-800 rounded-xl p-4 border border-gray-700 hover:border-gray-600 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-yellow-600 rounded-lg flex items-center justify-center">
                            <span class="text-lg">🏆</span>
                        </div>
                        <div>
                            <div class="text-white font-medium">Leaderboard</div>
                            <div class="text-xs text-gray-400">See top streaks</div>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
                
                <a href="" class="flex items-center justify-between bg-gray-800 rounded-xl p-4 border border-gray-700 hover:border-gray-600 transition-colors">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center">
                            <span class="text-lg">👤</span>
                        </div>
                        <div>
                            <div class="text-white font-medium">Profile & Settings</div>
                            <div class="text-xs text-gray-400">Customize your experience</div>
                        </div>
                    </div>
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
            @endauth
        </div>
    </div>

    @guest
    <!-- Feature Highlights for Non-Authenticated Users -->
    <div class="px-6 py-8 bg-gray-850">
        <h3 class="text-xl font-bold text-white mb-6 text-center">How OneTap Works</h3>
        
        <div class="space-y-6">
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold">1</span>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-1">Take One Photo</h4>
                    <p class="text-gray-400 text-sm">Capture your authentic moment - no filters, no editing, just real life.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold">2</span>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-1">See 5 Random Photos</h4>
                    <p class="text-gray-400 text-sm">Discover what 5 strangers around the world were doing at the same time.</p>
                </div>
            </div>
            
            <div class="flex items-start space-x-4">
                <div class="w-12 h-12 gradient-bg rounded-full flex items-center justify-center flex-shrink-0">
                    <span class="text-white font-bold">3</span>
                </div>
                <div>
                    <h4 class="text-white font-semibold mb-1">React & Connect</h4>
                    <p class="text-gray-400 text-sm">Share reactions and build your daily streak with the global community.</p>
                </div>
            </div>
        </div>
        
        <div class="mt-8 text-center">
            <a href="/register" class="btn-primary">
                🌟 Join OneTap Today
            </a>
            <p class="text-xs text-gray-500 mt-3">Free forever • No spam • Real connections</p>
        </div>
    </div>
    @endguest
</div>
@endsection

@push('scripts')
<script>
    // Countdown timer for next photo
    function updateCountdown() {
        const now = new Date();
        const tomorrow = new Date(now);
        tomorrow.setDate(tomorrow.getDate() + 1);
        tomorrow.setHours(0, 0, 0, 0);
        
        const timeLeft = tomorrow - now;
        
        const hours = Math.floor(timeLeft / (1000 * 60 * 60));
        const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);
        
        document.getElementById('hours').textContent = hours.toString().padStart(2, '0');
        document.getElementById('minutes').textContent = minutes.toString().padStart(2, '0');
        document.getElementById('seconds').textContent = seconds.toString().padStart(2, '0');
    }
    
    // Update countdown every second
    if (document.getElementById('countdown')) {
        updateCountdown();
        setInterval(updateCountdown, 1000);
    }
    
    // Take photo function
    function takePhoto() {
        // Add loading state
        const button = event.target;
        button.innerHTML = '<div class="spinner"></div> Opening Camera...';
        button.disabled = true;
        
        // Simulate camera opening delay
        setTimeout(() => {
            window.location.href = '/camera';
        }, 1000);
    }
    
    // Add some interactive animations
    document.addEventListener('DOMContentLoaded', function() {
        // Animate stats cards on load
        const statCards = document.querySelectorAll('[class*="grid-cols-2"] > div');
        statCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, index * 200);
        });
        
        // Animate photo grid
        const photoCards = document.querySelectorAll('.grid-cols-2 .aspect-square');
        photoCards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'scale(0.8)';
            setTimeout(() => {
                card.style.transition = 'all 0.4s ease';
                card.style.opacity = '1';
                card.style.transform = 'scale(1)';
            }, 500 + (index * 100));
        });
    });
</script>
@endpush

@push('styles')
<style>
    .bg-gray-850 {
        background-color: #1f2937;
    }
    
    /* Enhanced hover effects */
    .aspect-square:hover {
        transform: scale(1.02);
        transition: transform 0.2s ease;
    }
    
    /* Pulse animation for photo button */
    @keyframes pulse-ring {
        0% {
            transform: scale(0.33);
        }
        40%, 50% {
            opacity: 0;
        }
        100% {
            opacity: 0;
            transform: scale(1.2);
        }
    }
    
    .pulse-animation::before {
        content: '';
        position: absolute;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        animation: pulse-ring 2s infinite;
    }
</style>
@endpush