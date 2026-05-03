@extends('layouts.app')

@section('title', 'Notifications — OneTap')
@section('page-title', 'Notifications')

@section('header-actions')
<button onclick="markAllRead()"
    class="flex items-center gap-2 px-4 py-2 glass rounded-xl text-zinc-400 text-sm font-medium hover:text-zinc-200 hover:border-violet-500/30 transition-all active:scale-95">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
    </svg>
    Mark all read
</button>
@endsection

@section('content')
<div class="px-6 py-5">

    @php
        $today     = $notifications->filter(fn($n) => \Carbon\Carbon::parse($n->created_at)->isToday());
        $yesterday = $notifications->filter(fn($n) => \Carbon\Carbon::parse($n->created_at)->isYesterday());
        $earlier   = $notifications->filter(fn($n) => !\Carbon\Carbon::parse($n->created_at)->isToday() && !\Carbon\Carbon::parse($n->created_at)->isYesterday());
    @endphp

    @forelse($notifications as $notif)
    @php $data = json_decode($notif->data, true); @endphp
    @if($loop->first || ($loop->index > 0 && !$today->contains($notifications[$loop->index - 1]) && $today->contains($notif)))
    <div class="panel-section-header mb-3 mt-{{ $loop->first ? '0' : '6' }}">Today</div>
    @elseif($yesterday->contains($notif) && ($loop->first || !$yesterday->contains($notifications[$loop->index - 1])))
    <div class="panel-section-header mb-3 mt-6">Yesterday</div>
    @elseif($earlier->contains($notif) && ($loop->first || !$earlier->contains($notifications[$loop->index - 1])))
    <div class="panel-section-header mb-3 mt-6">Earlier</div>
    @endif

    <div class="flex items-start gap-4 p-4 rounded-2xl mb-2 transition-all
        {{ $notif->read_at ? 'glass' : 'bg-violet-500/5 border border-violet-500/20' }}
        hover:border-violet-500/25 group">

        {{-- Icon --}}
        <div class="w-11 h-11 rounded-2xl flex items-center justify-center flex-shrink-0 text-xl
            {{ $notif->read_at ? 'bg-[#0d0d1a] border border-[#1e1e35]' : 'bg-violet-500/10 border border-violet-500/20' }}">
            {{ match($data['type'] ?? '') {
                'new_reaction'        => '❤️',
                'new_comment'         => '💬',
                'confession_trending' => '🔥',
                'post_trending'       => '🚀',
                'friend_request'      => '🤝',
                'badge_earned'        => '🏆',
                'streak_milestone'    => '🔥',
                'rank_up'             => '⬆️',
                default               => '🔔',
            } }}
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <p class="text-sm text-zinc-200 leading-relaxed group-hover:text-white transition-colors">
                {{ $data['message'] ?? 'New notification' }}
            </p>
            <p class="text-xs text-zinc-600 mt-1 font-mono">
                {{ \Carbon\Carbon::parse($notif->created_at)->diffForHumans() }}
            </p>
        </div>

        {{-- Unread dot --}}
        @if(!$notif->read_at)
        <div class="w-2.5 h-2.5 bg-violet-500 rounded-full flex-shrink-0 mt-1 animate-pulse"></div>
        @endif
    </div>
    @empty
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-3xl glass flex items-center justify-center text-4xl mb-6 animate-float">🔔</div>
        <h3 class="font-display font-bold text-xl text-zinc-300 mb-2">All quiet here.</h3>
        <p class="text-zinc-600 text-sm">Go make some noise on campus.</p>
    </div>
    @endforelse

    {{ $notifications->links() }}
</div>
@endsection

@push('scripts')
<script>
async function markAllRead() {
    try {
        await fetch('/notifications/read-all', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
        });
        // Remove unread indicators
        document.querySelectorAll('.bg-violet-500\\/5').forEach(el => {
            el.classList.remove('bg-violet-500/5', 'border-violet-500/20');
            el.classList.add('glass');
        });
        document.querySelectorAll('.animate-pulse').forEach(el => el.remove());
    } catch(e) {}
}
</script>
@endpush
