@extends('layouts.app')

@section('title', 'Feed — OneTap')
@section('page-title', 'Feed')

@section('page-subtitle')
<div class="flex items-center gap-2 ml-2">
    <span class="w-1.5 h-1.5 bg-acid rounded-full animate-pulse"></span>
    <span class="text-xs text-zinc-500">
        <span class="text-acid font-mono font-semibold">{{ $onlineCount }}</span>
        active {{ $campusId ? 'on campus' : 'nearby' }}
    </span>
</div>
@endsection

@section('content')
<div class="px-6 py-5">

    {{-- ── Trending topics horizontal scroll ──────────────────────────── --}}
    @if($trending->count())
    <div class="mb-5">
        <div class="flex items-center gap-2 mb-3">
            <span class="text-ember text-sm">🔥</span>
            <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Trending Now</span>
        </div>
        <div class="flex gap-2 overflow-x-auto pb-2 scrollbar-none">
            @foreach($trending as $topic)
            <a href="{{ route('feed.index', ['tab' => 'trending']) }}"
                class="flex-shrink-0 flex items-center gap-2 px-4 py-2 glass rounded-full text-sm text-zinc-300 font-medium hover:border-violet-500/40 hover:text-violet-300 transition-all group">
                <span class="text-zinc-600 group-hover:text-violet-500 transition-colors">#</span>{{ $topic->topic }}
                <span class="text-xs font-mono text-zinc-600">{{ number_format($topic->post_count) }}</span>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Nearby events strip ─────────────────────────────────────────── --}}
    @if($nearbyEvents->count())
    <div class="mb-5">
        <div class="flex items-center justify-between mb-3">
            <div class="flex items-center gap-2">
                <span class="text-sm">📍</span>
                <span class="text-xs font-semibold text-zinc-500 uppercase tracking-wider">Nearby Events</span>
            </div>
            <a href="{{ route('events.index') }}" class="text-xs text-violet-400 hover:text-violet-300 transition-colors">See all →</a>
        </div>
        <div class="flex gap-3 overflow-x-auto pb-2 scrollbar-none">
            @foreach($nearbyEvents as $event)
            <a href="{{ route('events.index') }}"
                class="flex-shrink-0 w-52 glass rounded-2xl p-4 hover:border-violet-500/30 transition-all group">
                <div class="text-xs text-violet-400 font-semibold mb-1.5 truncate">📍 {{ $event->venue_name ?? 'Nearby' }}</div>
                <div class="text-sm text-zinc-200 font-semibold leading-tight mb-2 group-hover:text-white transition-colors">{{ Str::limit($event->title, 40) }}</div>
                <div class="text-xs text-zinc-600">{{ $event->starts_at->format('D, h:i A') }}</div>
                <div class="flex items-center gap-1 mt-2">
                    <span class="w-1.5 h-1.5 bg-acid rounded-full"></span>
                    <span class="text-xs text-zinc-500">{{ $event->attendee_count }} going</span>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Feed tabs ───────────────────────────────────────────────────── --}}
    <div class="flex gap-1.5 overflow-x-auto pb-1 mb-6 scrollbar-none">
        @foreach([
            ['nearby',    '📍', 'Nearby'],
            ['campus',    '🏫', 'Campus'],
            ['trending',  '🔥', 'Trending'],
            ['anonymous', '🎭', 'Confess'],
            ['friends',   '👥', 'Friends'],
            ['chaos',     '⚡', 'Chaos'],
        ] as [$key, $emoji, $label])
        <a href="{{ route('feed.index', ['tab' => $key]) }}"
            class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all
                {{ $tab === $key
                    ? 'bg-violet-600/20 text-violet-300 border border-violet-500/40 shadow-lg shadow-violet-500/10'
                    : 'text-zinc-500 hover:text-zinc-300 hover:bg-[#0d0d1a] border border-transparent' }}">
            <span>{{ $emoji }}</span>
            <span>{{ $label }}</span>
        </a>
        @endforeach
    </div>

    {{-- ── Feed grid ───────────────────────────────────────────────────── --}}
    @if($feeds->isEmpty())
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-3xl glass flex items-center justify-center text-4xl mb-6 animate-float">👻</div>
        <h3 class="font-display font-bold text-xl text-zinc-300 mb-2">Nothing here yet.</h3>
        <p class="text-zinc-600 text-sm mb-6">Be the first to drop something on this feed.</p>
        <button onclick="document.getElementById('post-modal').classList.remove('hidden')"
            class="btn-primary">
            Drop something 🔥
        </button>
    </div>
    @else

    <div class="space-y-4" id="feed-container">
        @foreach($feeds as $index => $item)
            @if($index === 0)
            {{-- Featured first post — full width, cinematic --}}
            <div class="relative feed-card overflow-hidden rounded-2xl p-0 group">
                <div class="absolute inset-0 bg-gradient-to-br from-violet-900/20 via-transparent to-fuchsia-900/20 pointer-events-none"></div>
                @include('partials.post-card', ['item' => $item, 'featured' => true])
            </div>
            @elseif($index % 5 === 0)
            {{-- Every 5th post — wide featured --}}
            <div class="feed-card overflow-hidden rounded-2xl group">
                @include('partials.post-card', ['item' => $item, 'featured' => true])
            </div>
            @else
            @include('partials.post-card', ['item' => $item])
            @endif
        @endforeach
    </div>

    {{-- Load more --}}
    <div class="flex justify-center py-8">
        <button id="load-more-btn" onclick="loadMorePosts()"
            class="flex items-center gap-2 px-6 py-3 glass rounded-2xl text-zinc-400 text-sm font-medium hover:text-violet-300 hover:border-violet-500/30 transition-all">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
            </svg>
            Load more
        </button>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
let currentPage = 1;
const currentTab = '{{ $tab }}';

async function loadMorePosts() {
    currentPage++;
    const btn = document.getElementById('load-more-btn');
    btn.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/></svg> Loading...';
    btn.disabled = true;

    try {
        const res = await fetch(`{{ route('feed.more') }}?tab=${currentTab}&page=${currentPage}`);
        const data = await res.json();

        if (data.posts && data.posts.length) {
            const container = document.getElementById('feed-container');
            data.posts.forEach(post => {
                container.insertAdjacentHTML('beforeend', buildPostCard(post));
            });
        }

        if (!data.has_more) {
            btn.innerHTML = '✓ All caught up';
            btn.disabled = true;
            btn.classList.add('opacity-40');
        } else {
            btn.innerHTML = '<svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg> Load more';
            btn.disabled = false;
        }
    } catch(e) {
        btn.innerHTML = 'Load more';
        btn.disabled = false;
    }
}

function buildPostCard(post) {
    return `
    <article class="feed-card overflow-hidden rounded-2xl p-5 animate-fade-in">
        <div class="flex items-center gap-3 mb-4">
            <img src="${post.author_avatar}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-[#1e1e35]">
            <div class="flex-1">
                <div class="font-semibold text-sm text-zinc-100">${post.author_name}</div>
                <div class="text-xs text-zinc-600">${post.created_at}</div>
            </div>
            ${post.is_trending ? '<span class="px-2.5 py-1 bg-ember/10 text-ember text-xs rounded-full border border-ember/20 font-semibold">🔥 trending</span>' : ''}
        </div>
        ${post.body ? `<p class="text-zinc-200 text-sm leading-relaxed">${post.body}</p>` : ''}
    </article>`;
}

// Realtime Feed listener
if (window.Echo) {
    const feedChannel = currentTab === 'campus' && '{{ $campusId }}' 
        ? `feed.campus.{{ $campusId }}`
        : 'feed.global';
        
    window.Echo.channel(feedChannel)
        .listen('.new.post', (post) => {
            // Only prepend if matching context
            if (currentTab === 'campus' && post.campus_id != '{{ $campusId }}') return;
            
            const container = document.getElementById('feed-container');
            if (container) {
                // Remove empty state if present
                const emptyState = container.parentElement.querySelector('.py-24');
                if (emptyState) emptyState.remove();
                
                // Prepend dynamically constructed post HTML
                container.insertAdjacentHTML('afterbegin', buildPostCard(post));
            }
        });
}
</script>
@endpush
