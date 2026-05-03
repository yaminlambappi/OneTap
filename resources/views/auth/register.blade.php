@extends('layouts.app')

@section('title', 'Join OneTap')

@section('content')
<div class="min-h-screen flex">

    {{-- ── LEFT: Cinematic brand panel ───────────────────────────────── --}}
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden flex-col items-center justify-center p-16">
        <div class="absolute inset-0 bg-gradient-to-br from-[#0d0d1a] via-[#07070f] to-[#030305]"></div>
        <div class="absolute inset-0 ambient-grid opacity-40"></div>
        <div class="absolute top-1/3 left-1/3 w-72 h-72 bg-fuchsia-600/12 rounded-full blur-3xl animate-float"></div>
        <div class="absolute bottom-1/3 right-1/3 w-56 h-56 bg-violet-600/12 rounded-full blur-3xl animate-float" style="animation-delay: -3s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-80 h-80 bg-cyan-600/5 rounded-full blur-3xl"></div>

        <div class="relative z-10 text-center max-w-md">
            <div class="w-20 h-20 rounded-3xl bg-gradient-to-br from-fuchsia-500 to-violet-500 flex items-center justify-center mx-auto mb-8 glow-fuchsia animate-pulse-glow">
                <img src="/OneTap.png" alt="OneTap" class="w-12 h-12 object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='block'">
                <span class="text-white font-black text-2xl hidden">OT</span>
            </div>

            <h1 class="font-display font-black text-7xl text-white mb-4 leading-none tracking-tight">
                Join the<br><span class="gradient-text">scene.</span>
            </h1>

            <p class="text-xl text-zinc-400 font-medium mb-10 leading-relaxed">
                Anonymous. Hyperlocal.<br>
                <span class="text-zinc-200 font-semibold">Unfiltered campus life.</span>
            </p>

            {{-- Steps --}}
            <div class="space-y-3 text-left">
                @foreach([
                    ['1', 'violet', 'Pick your username', 'Your identity on OneTap'],
                    ['2', 'fuchsia', 'Connect your campus', 'Find people near you'],
                    ['3', 'cyan', 'Start dropping', 'Posts, confessions, live rooms'],
                ] as [$num, $color, $title, $sub])
                <div class="flex items-center gap-4 glass rounded-xl px-4 py-3">
                    <div class="w-8 h-8 rounded-lg bg-{{ $color }}-500/20 border border-{{ $color }}-500/30 flex items-center justify-center flex-shrink-0">
                        <span class="text-{{ $color }}-400 text-xs font-mono font-bold">{{ $num }}</span>
                    </div>
                    <div>
                        <div class="text-sm font-semibold text-zinc-200">{{ $title }}</div>
                        <div class="text-xs text-zinc-600">{{ $sub }}</div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Register form ────────────────────────────────────────── --}}
    <div class="w-full lg:w-1/2 flex items-center justify-center p-8 lg:p-16 relative overflow-y-auto">
        <div class="w-full max-w-md animate-slide-up py-8">

            {{-- Mobile logo --}}
            <div class="lg:hidden text-center mb-8">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center mx-auto mb-3 glow-violet">
                    <span class="text-white font-black text-xl">OT</span>
                </div>
                <h1 class="font-display font-black text-3xl text-white">OneTap</h1>
            </div>

            <div class="mb-8">
                <h2 class="font-display font-black text-3xl text-white mb-2">Create account</h2>
                <p class="text-zinc-500">One tap into your local scene ⚡</p>
            </div>

            @if($errors->any())
            <div class="mb-6 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm space-y-1">
                @foreach($errors->all() as $error)
                <p>{{ $error }}</p>
                @endforeach
            </div>
            @endif

            <form method="POST" action="{{ route('register.post') }}" class="space-y-4">
                @csrf

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Username</label>
                    <input type="text" name="username" value="{{ old('username') }}" required autofocus
                        class="input-dark"
                        placeholder="cool_username">
                    <p class="text-zinc-700 text-xs mt-1.5">Letters, numbers, underscores only</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Phone Number</label>
                    <input type="text" name="phone" value="{{ old('phone') }}" required
                        class="input-dark"
                        placeholder="01XXXXXXXXX">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">
                        Campus
                        <span class="text-zinc-700 normal-case font-normal ml-1">(optional)</span>
                    </label>
                    <select name="campus_id" class="input-dark">
                        <option value="">Select your campus</option>
                        @foreach($campuses as $campus)
                        <option value="{{ $campus->id }}" {{ old('campus_id') == $campus->id ? 'selected' : '' }}>
                            {{ $campus->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" required
                        class="input-dark"
                        placeholder="Min 8 characters">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-500 uppercase tracking-wider mb-2">Confirm Password</label>
                    <input type="password" name="password_confirmation" required
                        class="input-dark"
                        placeholder="Repeat password">
                </div>

                <button type="submit" class="btn-primary w-full text-base py-3.5 mt-2">
                    Create account
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                    </svg>
                </button>
            </form>

            <div class="mt-8 pt-6 border-t border-[#1e1e35] text-center">
                <p class="text-sm text-zinc-600">
                    Already in?
                    <a href="{{ route('login') }}" class="text-violet-400 hover:text-violet-300 font-semibold transition-colors ml-1">Log in →</a>
                </p>
            </div>
        </div>
    </div>
</div>
@endsection
