@extends('layouts.app')

@section('title', 'Confessions — OneTap')
@section('page-title', 'Confession Wall')

@section('header-actions')
<button onclick="document.getElementById('confession-modal').classList.remove('hidden')"
    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-fuchsia-600 to-violet-600 hover:from-fuchsia-500 hover:to-violet-500 text-white text-sm font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-fuchsia-500/20 active:scale-95">
    <span>🎭</span> Confess
</button>
@endsection

@section('content')
<div class="px-6 py-5">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="mb-6">
        <div class="flex items-center gap-3 mb-2">
            <span class="text-4xl">🎭</span>
            <div>
                <h1 class="font-display font-black text-3xl text-white tracking-tight">CONFESSION WALL</h1>
                <p class="text-zinc-600 text-sm">Anonymous. Real. Unfiltered.</p>
            </div>
        </div>
        {{-- Atmospheric divider --}}
        <div class="h-px bg-gradient-to-r from-fuchsia-500/30 via-violet-500/20 to-transparent mt-4"></div>
    </div>

    {{-- ── Category filter pills ───────────────────────────────────────── --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none">
        @foreach([
            [null,       '🌐', 'All'],
            ['crush',    '💘', 'Crush'],
            ['academic', '📚', 'Academic'],
            ['social',   '🎉', 'Social'],
            ['rant',     '😤', 'Rant'],
            ['secret',   '🤫', 'Secret'],
        ] as [$cat, $emoji, $label])
        <a href="{{ route('confessions.index', $cat ? ['category' => $cat] : []) }}"
            class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all
                {{ $category === $cat
                    ? 'bg-fuchsia-600/20 text-fuchsia-300 border border-fuchsia-500/40 shadow-lg shadow-fuchsia-500/10'
                    : 'text-zinc-500 hover:text-zinc-300 hover:bg-[#0d0d1a] border border-transparent' }}">
            <span>{{ $emoji }}</span>
            <span>{{ $label }}</span>
        </a>
        @endforeach
    </div>

    {{-- ── Confession cards ────────────────────────────────────────────── --}}
    @forelse($confessions as $item)
    <div class="confession-border mb-4 overflow-hidden animate-fade-in">
        @include('partials.post-card', ['item' => $item])
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-3xl glass flex items-center justify-center text-4xl mb-6 animate-float">🤫</div>
        <h3 class="font-display font-bold text-xl text-zinc-300 mb-2">No confessions yet.</h3>
        <p class="text-zinc-600 text-sm mb-6">Be the first to spill something real.</p>
        <button onclick="document.getElementById('confession-modal').classList.remove('hidden')"
            class="flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-fuchsia-600 to-violet-600 text-white font-bold rounded-xl hover:shadow-lg hover:shadow-fuchsia-500/20 transition-all active:scale-95">
            🎭 Drop anonymously
        </button>
    </div>
    @endforelse

    {{ $confessions->links() }}
</div>

{{-- ── Floating confess button ─────────────────────────────────────────── --}}
<button onclick="document.getElementById('confession-modal').classList.remove('hidden')"
    class="fixed bottom-8 right-8 z-30 w-14 h-14 rounded-2xl bg-gradient-to-br from-fuchsia-600 to-violet-600 flex items-center justify-center text-2xl shadow-2xl glow-fuchsia hover:scale-110 transition-transform active:scale-95">
    🎭
</button>

{{-- ── Confession modal (centered overlay) ───────────────────────────── --}}
<div id="confession-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4"
     x-data="{ open: false }" @keydown.escape.window="document.getElementById('confession-modal').classList.add('hidden')">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md"
         onclick="document.getElementById('confession-modal').classList.add('hidden')"></div>

    <div class="relative w-full max-w-lg glass-dark rounded-3xl border border-fuchsia-500/20 p-6 animate-slide-up shadow-2xl shadow-fuchsia-500/10">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-3">
                <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-fuchsia-500/20 to-violet-500/20 border border-fuchsia-500/30 flex items-center justify-center text-2xl">
                    🎭
                </div>
                <div>
                    <p class="font-display font-bold text-zinc-100">Anonymous Confession</p>
                    <p class="text-xs text-zinc-600">Your identity is completely hidden</p>
                </div>
            </div>
            <button onclick="document.getElementById('confession-modal').classList.add('hidden')"
                class="p-2 rounded-xl hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('confessions.store') }}" method="POST" class="space-y-4">
            @csrf

            <select name="category" class="input-dark">
                <option value="general">🌐 General</option>
                <option value="crush">💘 Crush</option>
                <option value="academic">📚 Academic</option>
                <option value="social">🎉 Social</option>
                <option value="rant">😤 Rant</option>
                <option value="secret">🤫 Secret</option>
            </select>

            <textarea name="body" rows="5" required
                placeholder="Spill it. No one will know it's you..."
                class="input-dark resize-none focus:border-fuchsia-500/50 focus:shadow-[0_0_0_3px_rgba(192,38,211,0.1)]"></textarea>

            {{-- Mood selector --}}
            <div>
                <p class="text-xs text-zinc-600 uppercase tracking-wider font-semibold mb-2">Mood</p>
                <div class="flex gap-2">
                    @foreach(['😭','😍','🔥','💀','😂','😱'] as $mood)
                    <button type="button" onclick="selectMood('{{ $mood }}')"
                        class="mood-btn w-10 h-10 rounded-xl text-xl flex items-center justify-center hover:bg-[#1e1e35] transition-all opacity-40 hover:opacity-100 hover:scale-110"
                        data-mood="{{ $mood }}">{{ $mood }}</button>
                    @endforeach
                </div>
                <input type="hidden" name="mood" id="selected-mood">
            </div>

            <button type="submit"
                class="w-full py-3.5 bg-gradient-to-r from-fuchsia-600 to-violet-600 hover:from-fuchsia-500 hover:to-violet-500 text-white font-bold rounded-2xl transition-all hover:shadow-lg hover:shadow-fuchsia-500/20 active:scale-[0.98]">
                Drop anonymously 🎭
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
function selectMood(mood) {
    document.getElementById('selected-mood').value = mood;
    document.querySelectorAll('.mood-btn').forEach(btn => {
        const isSelected = btn.dataset.mood === mood;
        btn.classList.toggle('opacity-100', isSelected);
        btn.classList.toggle('opacity-40', !isSelected);
        btn.classList.toggle('bg-[#1e1e35]', isSelected);
        btn.classList.toggle('scale-110', isSelected);
    });
}
</script>
@endpush
@endsection
