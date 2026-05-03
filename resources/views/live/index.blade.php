@extends('layouts.app')

@section('title', 'Live Rooms — OneTap')
@section('page-title', 'Live Rooms')

@section('header-actions')
<button onclick="document.getElementById('room-modal').classList.remove('hidden')"
    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-acid/80 to-emerald-600 hover:from-acid hover:to-emerald-500 text-black text-sm font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-acid/20 active:scale-95">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
    </svg>
    Start Room
</button>
@endsection

@section('content')
<div class="px-6 py-5">

    {{-- ── LIVE NOW header ─────────────────────────────────────────────── --}}
    <div class="flex items-center gap-3 mb-6">
        <div class="flex items-center gap-2">
            <span class="live-dot w-3 h-3"></span>
            <span class="font-display font-black text-2xl text-white tracking-tight">LIVE NOW</span>
        </div>
        <span class="px-3 py-1 bg-acid/10 border border-acid/25 text-acid text-xs font-mono font-bold rounded-full">
            {{ $rooms->count() }} rooms
        </span>
    </div>

    {{-- ── Rooms grid ──────────────────────────────────────────────────── --}}
    @forelse($rooms as $room)
    <a href="{{ route('live.show', $room) }}"
        class="flex items-center gap-5 feed-card rounded-2xl p-5 mb-3 group hover:border-acid/20 transition-all">

        {{-- Room icon --}}
        <div class="w-14 h-14 rounded-2xl flex items-center justify-center flex-shrink-0 text-2xl
            {{ $room->participant_count > 10
                ? 'bg-acid/10 border border-acid/25 glow-acid'
                : 'bg-[#0d0d1a] border border-[#1e1e35]' }}">
            {{ $room->is_anonymous ? '🎭' : '💬' }}
        </div>

        {{-- Room info --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 mb-1">
                <p class="font-display font-bold text-zinc-100 truncate group-hover:text-white transition-colors">
                    {{ $room->topic }}
                </p>
                @if($room->participant_count > 10)
                <span class="flex-shrink-0 px-2 py-0.5 bg-ember/10 text-ember text-xs rounded-full border border-ember/20 font-semibold">🔥 Hot</span>
                @endif
            </div>
            <div class="flex items-center gap-3 flex-wrap">
                <span class="flex items-center gap-1.5 text-xs text-acid font-semibold">
                    <span class="live-dot w-1.5 h-1.5"></span>
                    <span class="font-mono">{{ $room->participant_count }}</span> live
                </span>
                @if($room->campus)
                <span class="text-xs text-zinc-600 flex items-center gap-1">
                    <span class="text-violet-600">🏫</span>
                    {{ $room->campus->short_name }}
                </span>
                @endif
                @if($room->is_anonymous)
                <span class="text-xs text-fuchsia-500 flex items-center gap-1">
                    👻 anonymous
                </span>
                @endif
                <span class="text-xs text-zinc-700 font-mono">{{ $room->created_at->diffForHumans() }}</span>
            </div>
        </div>

        {{-- Arrow --}}
        <svg class="w-5 h-5 text-zinc-700 group-hover:text-violet-400 group-hover:translate-x-1 transition-all flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
        </svg>
    </a>
    @empty
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-3xl glass flex items-center justify-center text-4xl mb-6 animate-float">💬</div>
        <h3 class="font-display font-bold text-xl text-zinc-300 mb-2">No live rooms right now.</h3>
        <p class="text-zinc-600 text-sm mb-6">Start one and see who joins.</p>
        <button onclick="document.getElementById('room-modal').classList.remove('hidden')"
            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-acid/80 to-emerald-600 text-black font-bold rounded-xl hover:shadow-lg hover:shadow-acid/20 transition-all active:scale-95">
            💬 Start a room
        </button>
    </div>
    @endforelse
</div>

{{-- ── Create room modal (centered overlay) ───────────────────────────── --}}
<div id="room-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md"
         onclick="document.getElementById('room-modal').classList.add('hidden')"></div>

    <div class="relative w-full max-w-md glass-dark rounded-3xl border border-acid/20 p-6 animate-slide-up shadow-2xl shadow-acid/5">
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-acid/10 border border-acid/25 flex items-center justify-center">
                    <span class="live-dot"></span>
                </div>
                <h2 class="font-display font-bold text-xl text-white">Start a Live Room</h2>
            </div>
            <button onclick="document.getElementById('room-modal').classList.add('hidden')"
                class="p-2 rounded-xl hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('live.store') }}" method="POST" class="space-y-4">
            @csrf

            <input type="text" name="topic" required placeholder="What's the topic?"
                class="input-dark focus:border-acid/50 focus:shadow-[0_0_0_3px_rgba(132,204,22,0.1)]">

            <div class="grid grid-cols-2 gap-3">
                <select name="type" class="input-dark">
                    <option value="open">🌐 Open</option>
                    <option value="campus">🏫 Campus only</option>
                </select>
                <input type="number" name="max_participants" min="2" max="100" value="50"
                    placeholder="Max people" class="input-dark">
            </div>

            <label class="flex items-center gap-3 cursor-pointer group">
                <div class="relative flex-shrink-0">
                    <input type="checkbox" name="is_anonymous" class="sr-only peer">
                    <div class="w-10 h-5 bg-[#1e1e35] rounded-full peer-checked:bg-fuchsia-600 transition-colors"></div>
                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-zinc-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-white"></div>
                </div>
                <span class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors">Anonymous room 👻</span>
            </label>

            <button type="submit"
                class="w-full py-3.5 bg-gradient-to-r from-acid/80 to-emerald-600 hover:from-acid hover:to-emerald-500 text-black font-bold rounded-2xl transition-all hover:shadow-lg hover:shadow-acid/20 active:scale-[0.98]">
                Start room 💬
            </button>
        </form>
    </div>
</div>
@endsection
