@extends('layouts.app')

@section('title', 'Events — OneTap')
@section('page-title', 'Events')

@section('header-actions')
<button onclick="document.getElementById('event-modal').classList.remove('hidden')"
    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-sm font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-violet-500/20 active:scale-95">
    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
    </svg>
    Create Event
</button>
@endsection

@section('content')
<div class="px-6 py-5">

    {{-- ── Filter bar ──────────────────────────────────────────────────── --}}
    <div class="flex gap-2 overflow-x-auto pb-2 mb-6 scrollbar-none">
        @foreach([
            ['all',      '🌐', 'All'],
            ['today',    '⚡', 'Today'],
            ['week',     '📅', 'This Week'],
            ['campus',   '🏫', 'Campus'],
            ['nearby',   '📍', 'Nearby'],
        ] as [$filter, $emoji, $label])
        <a href="{{ route('events.index', ['filter' => $filter]) }}"
            class="flex-shrink-0 flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold transition-all
                {{ (request('filter', 'all') === $filter)
                    ? 'bg-violet-600/20 text-violet-300 border border-violet-500/40'
                    : 'text-zinc-500 hover:text-zinc-300 hover:bg-[#0d0d1a] border border-transparent' }}">
            <span>{{ $emoji }}</span>
            <span>{{ $label }}</span>
        </a>
        @endforeach
    </div>

    @forelse($events as $index => $event)

    @if($index === 0)
    {{-- ── Hero event card ─────────────────────────────────────────────── --}}
    <div class="relative rounded-3xl overflow-hidden mb-6 group cursor-pointer"
         onclick="window.location='{{ route('events.index') }}'">
        {{-- Background --}}
        @if($event->cover_image)
        <img src="{{ $event->cover_image }}" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
        @else
        <div class="absolute inset-0 bg-gradient-to-br
            {{ match($event->type ?? 'social') {
                'party'       => 'from-fuchsia-900 via-violet-900 to-[#030305]',
                'academic'    => 'from-cyan-900 via-blue-900 to-[#030305]',
                'meetup'      => 'from-emerald-900 via-teal-900 to-[#030305]',
                'spontaneous' => 'from-ember/30 via-orange-900 to-[#030305]',
                default       => 'from-violet-900 via-fuchsia-900 to-[#030305]',
            } }}">
        </div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-[#030305] via-[#030305]/60 to-transparent"></div>
        <div class="absolute inset-0 ambient-grid opacity-20"></div>

        {{-- Content --}}
        <div class="relative z-10 p-8 pt-48">
            <div class="flex items-start justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-2 mb-3">
                        <span class="px-3 py-1 bg-white/10 backdrop-blur-sm rounded-full text-xs font-semibold text-white border border-white/20">
                            {{ match($event->type ?? 'social') {
                                'party'       => '🎉 Party',
                                'academic'    => '📚 Academic',
                                'meetup'      => '🤝 Meetup',
                                'spontaneous' => '⚡ Spontaneous',
                                default       => '📍 Event',
                            } }}
                        </span>
                        @if($event->starts_at->isToday())
                        <span class="px-3 py-1 bg-acid/20 backdrop-blur-sm rounded-full text-xs font-semibold text-acid border border-acid/30">
                            Today
                        </span>
                        @endif
                    </div>
                    <h2 class="font-display font-black text-3xl text-white mb-2 leading-tight">{{ $event->title }}</h2>
                    @if($event->description)
                    <p class="text-zinc-400 text-sm mb-4 max-w-lg">{{ Str::limit($event->description, 120) }}</p>
                    @endif
                    <div class="flex items-center gap-4 text-sm text-zinc-400">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-violet-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            {{ $event->starts_at->format('D, M j · h:i A') }}
                        </span>
                        @if($event->venue_name)
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-fuchsia-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            {{ $event->venue_name }}
                        </span>
                        @endif
                        <span class="flex items-center gap-1.5">
                            <span class="w-2 h-2 bg-acid rounded-full"></span>
                            <span class="text-acid font-semibold">{{ $event->attendee_count }}</span> going
                        </span>
                    </div>
                </div>
                @auth
                <div class="flex flex-col gap-2 flex-shrink-0">
                    <button onclick="attendEvent({{ $event->id }}, 'going')"
                        class="px-5 py-2.5 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-sm font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-violet-500/20 active:scale-95">
                        Going ✓
                    </button>
                    <button onclick="attendEvent({{ $event->id }}, 'maybe')"
                        class="px-5 py-2.5 glass text-zinc-400 text-sm font-semibold rounded-xl hover:text-zinc-200 transition-all active:scale-95">
                        Maybe
                    </button>
                </div>
                @endauth
            </div>
        </div>
    </div>

    @if($events->count() > 1)
    {{-- ── 3-column grid for remaining events ─────────────────────────── --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
    @endif

    @else
    {{-- Regular event card --}}
    <div class="feed-card rounded-2xl overflow-hidden group hover:border-violet-500/25 transition-all">
        @if($event->cover_image)
        <img src="{{ $event->cover_image }}" class="w-full h-40 object-cover group-hover:scale-105 transition-transform duration-500">
        @else
        <div class="h-32 bg-gradient-to-br
            {{ match($event->type ?? 'social') {
                'party'       => 'from-fuchsia-900/60 to-violet-900/60',
                'academic'    => 'from-cyan-900/60 to-blue-900/60',
                'meetup'      => 'from-emerald-900/60 to-teal-900/60',
                'spontaneous' => 'from-orange-900/60 to-red-900/60',
                default       => 'from-violet-900/60 to-fuchsia-900/60',
            } }} flex items-center justify-center">
            <span class="text-5xl opacity-60">
                {{ match($event->type ?? 'social') {
                    'party'       => '🎉',
                    'academic'    => '📚',
                    'meetup'      => '🤝',
                    'spontaneous' => '⚡',
                    default       => '📍',
                } }}
            </span>
        </div>
        @endif

        <div class="p-5">
            <div class="flex items-start justify-between gap-2 mb-3">
                <h3 class="font-display font-bold text-zinc-100 text-lg leading-tight group-hover:text-white transition-colors">{{ $event->title }}</h3>
                @if($event->starts_at->isToday())
                <span class="flex-shrink-0 px-2 py-0.5 bg-acid/15 text-acid text-xs rounded-full border border-acid/25 font-semibold">Today</span>
                @endif
            </div>

            @if($event->description)
            <p class="text-zinc-600 text-sm mb-3 leading-relaxed">{{ Str::limit($event->description, 80) }}</p>
            @endif

            <div class="space-y-1.5 mb-4">
                <div class="flex items-center gap-2 text-xs text-zinc-500">
                    <svg class="w-3.5 h-3.5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    {{ $event->starts_at->format('D, M j · h:i A') }}
                </div>
                @if($event->venue_name)
                <div class="flex items-center gap-2 text-xs text-zinc-500">
                    <svg class="w-3.5 h-3.5 text-fuchsia-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                    </svg>
                    {{ $event->venue_name }}
                </div>
                @endif
            </div>

            <div class="flex items-center justify-between">
                <div class="flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 bg-acid rounded-full"></span>
                    <span class="text-xs text-zinc-500">
                        <span class="text-acid font-semibold font-mono">{{ $event->attendee_count }}</span> going
                        @if($event->max_attendees)
                        <span class="text-zinc-700">/ {{ $event->max_attendees }}</span>
                        @endif
                    </span>
                </div>
                @auth
                <div class="flex gap-2">
                    <button onclick="attendEvent({{ $event->id }}, 'going')"
                        class="px-3 py-1.5 bg-violet-600/20 hover:bg-violet-600/30 text-violet-300 text-xs font-semibold rounded-xl border border-violet-500/30 transition-all active:scale-95">
                        Going
                    </button>
                    <button onclick="attendEvent({{ $event->id }}, 'maybe')"
                        class="px-3 py-1.5 bg-[#0d0d1a] hover:bg-[#1e1e35] text-zinc-500 text-xs font-semibold rounded-xl border border-[#1e1e35] transition-all active:scale-95">
                        Maybe
                    </button>
                </div>
                @endauth
            </div>
        </div>
    </div>

    @if($loop->last && $events->count() > 1)
    </div>
    @endif
    @endif

    @empty
    <div class="flex flex-col items-center justify-center py-24 text-center">
        <div class="w-20 h-20 rounded-3xl glass flex items-center justify-center text-4xl mb-6 animate-float">📍</div>
        <h3 class="font-display font-bold text-xl text-zinc-300 mb-2">No events yet.</h3>
        <p class="text-zinc-600 text-sm mb-6">Create a spontaneous meetup and see who shows up.</p>
        <button onclick="document.getElementById('event-modal').classList.remove('hidden')"
            class="btn-primary">
            Create Event 📍
        </button>
    </div>
    @endforelse

    {{ $events->links() }}
</div>

{{-- ── Create event modal (centered overlay) ──────────────────────────── --}}
<div id="event-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md"
         onclick="document.getElementById('event-modal').classList.add('hidden')"></div>

    <div class="relative w-full max-w-lg glass-dark rounded-3xl border border-violet-500/20 p-6 animate-slide-up shadow-2xl shadow-violet-500/10 max-h-[90vh] overflow-y-auto scrollbar-thin">
        <div class="flex items-center justify-between mb-6">
            <h2 class="font-display font-bold text-xl text-white">Create Event</h2>
            <button onclick="document.getElementById('event-modal').classList.add('hidden')"
                class="p-2 rounded-xl hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <form action="{{ route('events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf

            <input type="text" name="title" required placeholder="Event title"
                class="input-dark">

            <textarea name="description" rows="2" placeholder="What's this about?"
                class="input-dark resize-none"></textarea>

            <div class="grid grid-cols-2 gap-3">
                <select name="type" class="input-dark">
                    <option value="social">🎉 Social</option>
                    <option value="academic">📚 Academic</option>
                    <option value="party">🎊 Party</option>
                    <option value="meetup">🤝 Meetup</option>
                    <option value="spontaneous">⚡ Spontaneous</option>
                </select>
                <input type="text" name="venue_name" placeholder="Venue name"
                    class="input-dark">
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="text-xs text-zinc-600 mb-1.5 block uppercase tracking-wider font-semibold">Starts at</label>
                    <input type="datetime-local" name="starts_at" required class="input-dark">
                </div>
                <div>
                    <label class="text-xs text-zinc-600 mb-1.5 block uppercase tracking-wider font-semibold">Max attendees</label>
                    <input type="number" name="max_attendees" min="2" max="500" placeholder="Unlimited"
                        class="input-dark">
                </div>
            </div>

            <button type="submit" class="btn-primary w-full py-3.5">
                Create event 📍
            </button>
        </form>
    </div>
</div>

@push('scripts')
<script>
async function attendEvent(eventId, status) {
    try {
        const res = await fetch(`/events/${eventId}/attend`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({ status }),
        });
        const data = await res.json();
        if (data.success) {
            // Could update button state here
        }
    } catch(e) {}
}
</script>
@endpush
@endsection
