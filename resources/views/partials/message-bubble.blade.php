<div class="flex items-start gap-3 animate-fade-in">
    @php
        $avatar = $message->is_anonymous
            ? "https://api.dicebear.com/7.x/bottts/svg?seed={$message->anonymous_avatar_seed}"
            : ($message->user?->avatar_url ?? '');
        $name = $message->is_anonymous
            ? ($message->anonymous_alias ?? 'Anonymous')
            : ($message->user?->display_name ?? 'Unknown');
    @endphp

    <img src="{{ $avatar }}" class="w-8 h-8 rounded-xl flex-shrink-0 mt-0.5 object-cover ring-2 ring-[#1e1e35]">

    <div class="flex-1 min-w-0">
        <div class="flex items-center gap-2 mb-1">
            <span class="text-xs font-semibold text-zinc-300">{{ $name }}</span>
            @if($message->is_anonymous)
            <span class="text-xs text-fuchsia-400 font-mono">anon</span>
            @endif
            <span class="text-xs text-zinc-700 font-mono">{{ $message->created_at->diffForHumans() }}</span>
        </div>
        <div class="glass rounded-2xl rounded-tl-sm px-4 py-2.5 text-sm text-zinc-200 max-w-sm break-words inline-block">
            {{ $message->body }}
        </div>
    </div>
</div>
