@extends('layouts.app')

@section('title', '@' . $user->username . ' — OneTap')
@section('page-title', $user->display_name)

@section('content')
<div>
    {{-- ── Cinematic cover header ──────────────────────────────────────── --}}
    <div class="relative h-64 overflow-hidden">
        @if($user->cover_image)
        <img src="{{ $user->cover_image }}" class="w-full h-full object-cover">
        @else
        <div class="absolute inset-0 bg-gradient-to-br from-violet-900/80 via-fuchsia-900/50 to-[#030305]"></div>
        <div class="absolute inset-0 ambient-grid opacity-30"></div>
        @endif
        {{-- Gradient fade to content --}}
        <div class="absolute inset-0 bg-gradient-to-t from-[#030305] via-[#030305]/40 to-transparent"></div>
        {{-- Orbs --}}
        <div class="absolute top-8 left-16 w-32 h-32 bg-violet-500/20 rounded-full blur-3xl"></div>
        <div class="absolute bottom-8 right-16 w-40 h-40 bg-fuchsia-500/15 rounded-full blur-3xl"></div>
    </div>

    <div class="px-8 -mt-16 relative z-10">
        {{-- ── Avatar + actions row ────────────────────────────────────── --}}
        <div class="flex items-end justify-between mb-6">
            <div class="relative">
                <img src="{{ $user->avatar_url }}" alt="avatar"
                    class="w-24 h-24 rounded-3xl object-cover ring-4 ring-[#030305] shadow-2xl">
                @if($user->isOnline())
                <span class="absolute -bottom-1 -right-1 w-4 h-4 bg-acid rounded-full ring-3 ring-[#030305] glow-acid"></span>
                @endif
            </div>

            <div class="flex gap-3 pb-2">
                @auth
                    @if(auth()->id() === $user->id)
                    <a href="{{ route('profile.edit') }}"
                        class="flex items-center gap-2 px-5 py-2.5 glass rounded-xl text-zinc-300 text-sm font-semibold hover:text-white hover:border-violet-500/30 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                        </svg>
                        Edit Profile
                    </a>
                    @elseif(!$isBlocked)
                    <button onclick="toggleFriend({{ $user->id }})" id="friend-btn"
                        class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm font-bold transition-all active:scale-95
                            {{ $isFriend
                                ? 'glass text-zinc-300 hover:border-red-500/30 hover:text-red-400'
                                : 'bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white hover:shadow-lg hover:shadow-violet-500/20' }}">
                        {{ $isFriend ? '✓ Connected' : '+ Connect' }}
                    </button>
                    @endif
                @endauth
            </div>
        </div>

        {{-- ── Identity info ───────────────────────────────────────────── --}}
        <div class="mb-6">
            <div class="flex items-center gap-3 flex-wrap mb-1">
                <h1 class="font-display font-black text-3xl text-white">{{ $user->display_name }}</h1>
                @if($user->is_verified)
                <span class="text-cyan-400 text-lg">✓</span>
                @endif
                <span class="px-3 py-1 text-xs rounded-full font-bold border
                    {{ match($user->influence_level ?? 'ember') {
                        'inferno' => 'bg-orange-500/15 text-orange-400 border-orange-500/25',
                        'flame'   => 'bg-red-500/15 text-red-400 border-red-500/25',
                        'spark'   => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/25',
                        default   => 'bg-zinc-800 text-zinc-500 border-zinc-700',
                    } }}">
                    {{ ucfirst($user->influence_level ?? 'ember') }}
                </span>
            </div>
            <p class="text-zinc-500 text-sm mb-1 font-mono">@{{ $user->username }}</p>
            @if($user->campus)
            <p class="text-zinc-600 text-xs flex items-center gap-1.5">
                <span class="text-violet-500">🏫</span>
                {{ $user->campus->name }}
            </p>
            @endif
            @if($user->bio)
            <p class="text-zinc-300 text-sm mt-3 leading-relaxed max-w-xl">{{ $user->bio }}</p>
            @endif

            {{-- Vibe tags --}}
            @if($user->vibe_tags)
            <div class="flex gap-2 flex-wrap mt-3">
                @foreach($user->vibe_tags as $tag)
                <span class="px-3 py-1 bg-[#0d0d1a] border border-[#1e1e35] text-zinc-500 text-xs rounded-full hover:border-violet-500/30 hover:text-violet-400 transition-colors cursor-default">
                    #{{ $tag }}
                </span>
                @endforeach
            </div>
            @endif
        </div>

        {{-- ── Stats row ───────────────────────────────────────────────── --}}
        <div class="grid grid-cols-4 gap-3 mb-6">
            @foreach([
                ['🔥', $user->streak?->current_streak ?? 0, 'Streak', 'text-ember'],
                ['⚡', $user->chaos_score ?? 0, 'Chaos', 'text-fuchsia-400'],
                ['🎭', $user->mystery_score ?? 0, 'Mystery', 'text-violet-400'],
                ['🏆', $user->reputation_score ?? 0, 'Rep', 'text-cyan-400'],
            ] as [$emoji, $value, $label, $color])
            <div class="glass rounded-2xl p-4 text-center group hover:border-violet-500/25 transition-all">
                <div class="text-2xl mb-1">{{ $emoji }}</div>
                <div class="text-2xl font-display font-black {{ $color }} font-mono">{{ number_format($value) }}</div>
                <div class="text-xs text-zinc-600 uppercase tracking-wider mt-0.5">{{ $label }}</div>
            </div>
            @endforeach
        </div>

        {{-- ── Badges ──────────────────────────────────────────────────── --}}
        @if($badges)
        <div class="mb-6">
            <p class="panel-section-header mb-3">Badges</p>
            <div class="flex gap-2 flex-wrap">
                @foreach($badges as $key => $badge)
                <div class="flex items-center gap-2 px-4 py-2 glass rounded-xl hover:border-violet-500/25 transition-all">
                    <span class="text-lg">{{ $badge['emoji'] }}</span>
                    <span class="text-xs text-zinc-300 font-semibold">{{ $badge['label'] }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Posts grid ──────────────────────────────────────────────── --}}
        <div class="mb-8">
            <p class="panel-section-header mb-4">Moments</p>
            @if($posts->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 glass rounded-2xl text-center">
                <span class="text-4xl mb-3">🌑</span>
                <p class="text-zinc-600 text-sm">No public posts yet.</p>
            </div>
            @else
            <div class="grid grid-cols-3 gap-2 rounded-2xl overflow-hidden">
                @foreach($posts as $post)
                <div class="aspect-square bg-[#0d0d1a] relative overflow-hidden group cursor-pointer rounded-xl">
                    @if($post->media)
                    <img src="{{ $post->media[0]['url'] }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    @else
                    <div class="w-full h-full flex items-center justify-center p-3 bg-gradient-to-br from-[#0d0d1a] to-[#1e1e35]">
                        <p class="text-zinc-500 text-xs text-center leading-tight">{{ Str::limit($post->body, 60) }}</p>
                    </div>
                    @endif
                    {{-- Hover overlay --}}
                    <div class="absolute inset-0 bg-gradient-to-t from-[#030305]/80 to-transparent opacity-0 group-hover:opacity-100 transition-opacity flex items-end p-3">
                        <div class="flex items-center gap-2 text-xs text-zinc-300">
                            @if($post->is_trending)
                            <span>🔥</span>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
            {{ $posts->links() }}
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
async function toggleFriend(userId) {
    const btn = document.getElementById('friend-btn');
    try {
        const res = await fetch(`/users/${userId}/friend`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
        });
        const data = await res.json();
        if (data.status === 'connected') {
            btn.textContent = '✓ Connected';
            btn.classList.remove('bg-gradient-to-r', 'from-violet-600', 'to-fuchsia-600', 'text-white');
            btn.classList.add('glass', 'text-zinc-300');
        } else {
            btn.textContent = '+ Connect';
            btn.classList.add('bg-gradient-to-r', 'from-violet-600', 'to-fuchsia-600', 'text-white');
            btn.classList.remove('glass', 'text-zinc-300');
        }
    } catch(e) {}
}
</script>
@endpush
