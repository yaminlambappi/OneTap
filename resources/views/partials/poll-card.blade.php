<div class="space-y-2" id="poll-{{ $poll->id }}">
    <p class="text-sm font-semibold text-zinc-200 mb-3">{{ $poll->question }}</p>

    <div class="space-y-2" id="poll-options-{{ $poll->id }}">
        @foreach($poll->options as $option)
        @php
            $pct = $poll->total_votes > 0 ? round(($option->vote_count / $poll->total_votes) * 100) : 0;
        @endphp
        <button
            onclick="votePoll({{ $poll->id }}, {{ $option->id }})"
            class="poll-option-btn relative w-full text-left px-4 py-3 rounded-xl border border-[#1e1e35] overflow-hidden group hover:border-violet-500/40 transition-all"
            data-option="{{ $option->id }}">
            {{-- Animated progress bar --}}
            <div class="absolute inset-0 bg-gradient-to-r from-violet-500/15 to-fuchsia-500/10 transition-all duration-700 ease-out rounded-xl"
                style="width: {{ $pct }}%"></div>
            {{-- Content --}}
            <div class="relative flex items-center justify-between gap-3">
                <span class="text-sm text-zinc-200 font-medium">{{ $option->emoji ?? '' }} {{ $option->label }}</span>
                <div class="flex items-center gap-2 flex-shrink-0">
                    <span class="text-xs font-mono text-zinc-500">{{ $option->vote_count }}</span>
                    <span class="text-xs font-mono font-bold text-violet-400 w-10 text-right">{{ $pct }}%</span>
                </div>
            </div>
        </button>
        @endforeach
    </div>

    <div class="flex items-center justify-between pt-1">
        <span class="text-xs text-zinc-600 font-mono" id="poll-votes-{{ $poll->id }}">
            {{ $poll->total_votes }} {{ Str::plural('vote', $poll->total_votes) }}
        </span>
        @if($poll->ends_at)
        <span class="text-xs text-zinc-600 font-mono">
            {{ $poll->isExpired() ? '⏱ Ended' : '⏱ Ends ' . $poll->ends_at->diffForHumans() }}
        </span>
        @endif
    </div>
</div>
