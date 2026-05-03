@extends('layouts.app')

@section('title', 'Login — OneTap')

@section('content')
<div class="min-h-screen flex">

    {{-- ── LEFT: Cinematic brand panel ───────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col items-center justify-center p-16">
        {{-- Layered background --}}
        <div class="absolute inset-0 bg-gradient-to-br from-[#0d0d1a] via-[#07070f] to-[#030305]"></div>
        <div class="absolute inset-0 ambient-grid opacity-40"></div>
        {{-- Orbs --}}
        <div class="absolute top-1/4 left-1/4 w-64 h-64 bg-violet-600/15 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-1/4 right-1/4 w-48 h-48 bg-fuchsia-600/15 rounded-full blur-3xl animate-float" style="animation-delay: -2s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-96 h-96 bg-cyan-600/5 rounded-full blur-3xl"></div>

        {{-- Content --}}
        <div class="relative z-10 text-center max-w-md">
            {{-- Logo mark --}}
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center mx-auto mb-8 glow-violet animate-pulse-glow">
                <img src="/OneTap.png" alt="OneTap" class="w-12 h-12 object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span class="text-white font-black text-2xl hidden">OT</span>
            </div>

            {{-- Wordmark --}}
            <h1 class="font-display font-black text-7xl text-white mb-4 leading-none tracking-tight">
                One<span class="gradient-text">Tap</span>
            </h1>

            {{-- Tagline --}}
            <p class="text-xl text-zinc-400 font-medium mb-10 leading-relaxed">
                Your campus.<br>
                <span class="text-zinc-200 font-semibold">Right now.</span>
            </p>

            {{-- Feature pills --}}
            <div class="flex flex-wrap gap-2 justify-center mb-10">
                @foreach(['📍 Hyperlocal', '🎭 Anonymous', '⚡ Live Rooms', '🔥 Trending', '🏫 Campus-first'] as $feat)
                <span class="px-3 py-1.5 glass rounded-full text-xs text-zinc-400 font-medium">{{ $feat }}</span>
                @endforeach
            </div>

            {{-- Social proof --}}
            <div class="glass rounded-2xl px-6 py-4 inline-flex items-center gap-3">
                <div class="flex -space-x-2">
                    @foreach(['violet', 'fuchsia', 'cyan', 'emerald'] as $color)
                    <div class="w-8 h-8 rounded-full bg-{{ $color }}-500/30 border-2 border-[#07070f] flex items-center justify-center text-xs">
                        {{ ['🧑', '👩', '🧑', '👨'][$loop->index] }}
                    </div>
                    @endforeach
                </div>
                <div class="text-left">
                    <div class="text-sm font-semibold text-zinc-200">
                        <span class="text-acid font-mono" id="active-count">247</span> students active
                    </div>
                    <div class="text-xs text-zinc-600">right now on campus</div>
                </div>
                <span class="live-dot ml-1"></span>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Login form ───────────────────────────────────────────── --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 relative">
        <div class="w-full max-w-md animate-slide-up">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center mx-auto mb-3 glow-violet">
                    <span class="text-white font-black text-xl">OT</span>
                </div>
                <h1 class="font-display font-black text-3xl text-white">OneTap</h1>
            </div>

            <div class="mb-8">
                <h2 class="font-display font-black text-3xl text-white mb-2">Welcome back</h2>
                <p class="text-zinc-500">Your campus is waiting 👀</p>
            </div>

            @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm">
                {{ $errors->first() }}
            </div>
            @endif

            <form method="POST" action="{{ route('login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="input-dark"
                        placeholder="your_username">
                    @error('username')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" required
                        class="input-dark"
                        placeholder="••••••••">
                    @error('password')
                    <p class="text-red-400 text-xs mt-1.5">{{ $message }}</p>
                    @enderror
                </div>

                <label class="flex items-center gap-3 cursor-pointer group">
                    <div class="relative flex-shrink-0">
                        <input type="checkbox" name="remember" class="sr-only peer">
                        <div class="w-10 h-5 bg-[#1e1e35] rounded-full peer-checked:bg-violet-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-zinc-400 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-white"></div>
                    </div>
                    <span class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors">Keep me logged in</span>
                </label>

                <button type="submit" class="btn-primary w-full text-base py-3.5">
                    Log in
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-[#1e1e35] text-center">
                <p class="text-sm text-zinc-600">
                    New here?
                    <a href="{{ route('register') }}" class="text-violet-400 hover:text-violet-300 font-semibold transition-colors ml-1">Create account →</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
