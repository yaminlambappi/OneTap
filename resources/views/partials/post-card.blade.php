@php
    $isPost       = $item instanceof \App\Models\Post;
    $isConfession = $item instanceof \App\Models\Confession;
    $itemType     = $isPost ? 'post' : 'confession';
    $itemId       = $item->id;
    $reactions    = \App\Models\Reaction::emojis();
    $featured     = $featured ?? false;
@endphp

<article class="{{ $featured ? 'p-6' : 'feed-card rounded-2xl p-5' }} overflow-hidden"
    data-id="{{ $itemId }}" data-type="{{ $itemType }}">

    {{-- ── Header ──────────────────────────────────────────────────────── --}}
    <div class="flex items-start gap-3 mb-4">
        <div class="relative flex-shrink-0">
            <img src="{{ $isConfession ? $item->avatar_url : $item->author_avatar }}"
                alt="avatar"
                class="w-10 h-10 rounded-xl object-cover ring-2
                    {{ $isConfession ? 'ring-fuchsia-500/30' : 'ring-[#1e1e35]' }}">
        </div>

        <div class="flex-1 min-w-0">
            <div class="flex items-center gap-2 flex-wrap mb-0.5">
                <span class="font-semibold text-sm text-zinc-100">
                    {{ $isConfession ? $item->anonymous_alias : $item->author_name }}
                </span>

                @if($isConfession)
                    <span class="px-2 py-0.5 bg-fuchsia-500/10 text-fuchsia-400 text-xs rounded-full border border-fuchsia-500/20 font-semibold">
                        🎭 confession
                    </span>
                    @if($item->category !== 'general')
                    <span class="px-2 py-0.5 bg-[#0d0d1a] text-zinc-500 text-xs rounded-full border border-[#1e1e35]">
                        {{ $item->category }}
                    </span>
                    @endif
                @elseif($isPost)
                    @if($item->is_anonymous)
                    <span class="px-2 py-0.5 bg-violet-500/10 text-violet-400 text-xs rounded-full border border-violet-500/20 font-semibold">
                        👻 anon
                    </span>
                    @endif
                    @if($item->is_trending)
                    <span class="px-2 py-0.5 bg-ember/10 text-ember text-xs rounded-full border border-ember/20 font-semibold">
                        🔥 trending
                    </span>
                    @endif
                    @if($item->type && $item->type !== 'text')
                    <span class="px-2 py-0.5 bg-[#0d0d1a] text-zinc-500 text-xs rounded-full border border-[#1e1e35]">
                        {{ str_replace('_', ' ', $item->type) }}
                    </span>
                    @endif
                @endif
            </div>
            <span class="text-xs text-zinc-600 font-mono">{{ $item->created_at->diffForHumans() }}</span>
        </div>

        {{-- Options menu --}}
        @auth
        <div class="relative flex-shrink-0" x-data="{ open: false }">
            <button @click="open = !open"
                class="p-1.5 rounded-lg hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 5a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3zm0 7a1.5 1.5 0 110-3 1.5 1.5 0 010 3z"/>
                </svg>
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute right-0 top-8 w-40 glass-dark rounded-2xl shadow-2xl z-20 overflow-hidden border border-[#1e1e35]">
                <button onclick="reportContent('{{ $itemType }}', {{ $itemId }})"
                    class="w-full text-left px-4 py-3 text-sm text-zinc-400 hover:bg-[#1e1e35] hover:text-zinc-200 transition-colors flex items-center gap-2">
                    <span>🚩</span> Report
                </button>
                @if(auth()->id() === ($item->user_id ?? null))
                <form action="{{ route('posts.destroy', $itemId) }}" method="POST">
                    @csrf @method('DELETE')
                    <button type="submit"
                        class="w-full text-left px-4 py-3 text-sm text-red-400 hover:bg-red-500/10 transition-colors flex items-center gap-2">
                        <span>🗑</span> Delete
                    </button>
                </form>
                @endif
            </div>
        </div>
        @endauth
    </div>

    {{-- ── Body ─────────────────────────────────────────────────────────── --}}
    @if($item->body)
    <div class="mb-4">
        <p class="text-zinc-200 text-sm leading-relaxed {{ $featured ? 'text-base' : '' }}">{{ $item->body }}</p>
    </div>
    @endif

    {{-- ── Media ────────────────────────────────────────────────────────── --}}
    @if($isPost && $item->media)
    <div class="mb-4">
        <div class="{{ count($item->media) > 1 ? 'grid grid-cols-2 gap-1.5' : '' }} rounded-2xl overflow-hidden">
            @foreach($item->media as $media)
            <img src="{{ $media['url'] }}" alt="post media"
                class="w-full object-cover {{ count($item->media) === 1 ? 'max-h-96 rounded-2xl' : 'aspect-square' }} hover:opacity-90 transition-opacity cursor-pointer">
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── Poll ─────────────────────────────────────────────────────────── --}}
    @if($isPost && $item->poll)
    <div class="mb-4">
        @include('partials.poll-card', ['poll' => $item->poll])
    </div>
    @endif

    {{-- ── Mood (confessions) ───────────────────────────────────────────── --}}
    @if($isConfession && $item->mood)
    <div class="mb-4">
        <span class="text-4xl">{{ $item->mood }}</span>
    </div>
    @endif

    {{-- ── Reaction bar ─────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-1.5 flex-wrap pt-3 border-t border-[#1e1e35]">
        @foreach($reactions as $type => $emoji)
        <button
            onclick="toggleReaction('{{ $itemType }}', {{ $itemId }}, '{{ $type }}')"
            class="flex items-center gap-1 px-2.5 py-1.5 rounded-xl bg-[#0d0d1a] hover:bg-[#1e1e35] border border-[#1e1e35] hover:border-violet-500/25 text-xs transition-all active:scale-95 group"
            data-type="{{ $type }}" data-id="{{ $itemId }}">
            <span class="group-hover:scale-125 transition-transform">{{ $emoji }}</span>
            <span class="reaction-count text-zinc-600 font-mono" data-reaction="{{ $type }}">
                {{ \App\Models\Reaction::where('reactable_type', $isPost ? \App\Models\Post::class : \App\Models\Confession::class)->where('reactable_id', $itemId)->where('type', $type)->count() ?: '' }}
            </span>
        </button>
        @endforeach

        {{-- Comment toggle --}}
        <button onclick="toggleComments({{ $itemId }}, '{{ $itemType }}')"
            class="ml-auto flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-[#0d0d1a] hover:bg-[#1e1e35] border border-[#1e1e35] hover:border-violet-500/25 text-xs text-zinc-500 hover:text-zinc-300 transition-all">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
            </svg>
            <span class="font-mono">{{ $item->comment_count ?: '' }}</span>
        </button>
    </div>

    {{-- ── Comment section (collapsed) ─────────────────────────────────── --}}
    <div id="comments-{{ $itemId }}-{{ $itemType }}" class="hidden mt-4 pt-4 border-t border-[#1e1e35]">
        <div class="space-y-3 mb-3" id="comments-list-{{ $itemId }}-{{ $itemType }}"></div>
        @auth
        <div class="flex items-center gap-2">
            <img src="{{ auth()->user()->avatar_url }}" class="w-7 h-7 rounded-lg flex-shrink-0 object-cover">
            <div class="flex-1 flex items-center gap-2 glass rounded-xl px-3 py-2">
                <input type="text"
                    placeholder="Reply..."
                    class="flex-1 bg-transparent text-sm text-zinc-200 placeholder-zinc-600 focus:outline-none"
                    onkeydown="if(event.key==='Enter') submitComment(this, {{ $itemId }}, '{{ $itemType }}')">
                <label class="flex items-center gap-1 text-xs text-zinc-600 cursor-pointer hover:text-fuchsia-400 transition-colors flex-shrink-0">
                    <input type="checkbox" class="anon-comment-toggle w-3 h-3 accent-fuchsia-500">
                    <span>anon</span>
                </label>
            </div>
        </div>
        @endauth
    </div>
</article>
