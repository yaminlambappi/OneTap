@extends('layouts.app')

@section('title', $room->topic . ' — Live')
@section('page-title', '')

@section('content')
<div class="flex h-[calc(100vh-56px)]">

    {{-- ── LEFT: Participant list (collapsible) ───────────────────────── --}}
    <div class="w-64 flex-shrink-0 border-r border-[#1e1e35] flex flex-col bg-[#07070f]/50"
         x-data="{ collapsed: false }">

        {{-- Header --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-[#1e1e35]">
            <div class="flex items-center gap-2">
                <span class="live-dot w-2 h-2"></span>
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Participants</span>
            </div>
            <div class="flex items-center gap-2">
                <span class="text-xs font-mono text-acid" id="participant-count">{{ $room->participant_count }}</span>
                <button @click="collapsed = !collapsed"
                    class="p-1 rounded-lg hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors">
                    <svg class="w-4 h-4 transition-transform" :class="collapsed ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Room info --}}
        <div class="px-4 py-3 border-b border-[#1e1e35]" x-show="!collapsed">
            <p class="font-display font-bold text-zinc-100 text-sm mb-1">{{ $room->topic }}</p>
            <div class="flex items-center gap-2 flex-wrap">
                @if($room->is_anonymous)
                <span class="text-xs text-fuchsia-400">👻 anonymous</span>
                @endif
                @if($room->campus)
                <span class="text-xs text-zinc-600">🏫 {{ $room->campus->short_name }}</span>
                @endif
            </div>
        </div>

        {{-- Participant list --}}
        <div class="flex-1 overflow-y-auto scrollbar-thin px-3 py-3 space-y-1" x-show="!collapsed" id="participants-list">
            @foreach($room->participants ?? [] as $participant)
            <div class="flex items-center gap-2.5 px-2 py-2 rounded-xl hover:bg-[#0d0d1a] transition-colors">
                <div class="relative flex-shrink-0">
                    <img src="{{ $participant->user?->avatar_url ?? 'https://api.dicebear.com/7.x/bottts/svg?seed=' . $participant->id }}"
                        class="w-7 h-7 rounded-full object-cover">
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-acid rounded-full ring-1 ring-[#07070f]"></span>
                </div>
                <span class="text-xs text-zinc-400 truncate">
                    {{ $room->is_anonymous ? 'Anonymous' : ($participant->user?->display_name ?? 'Unknown') }}
                </span>
            </div>
            @endforeach
        </div>

        {{-- Leave button --}}
        <div class="p-3 border-t border-[#1e1e35]" x-show="!collapsed">
            <a href="{{ route('live.index') }}"
                class="flex items-center justify-center gap-2 w-full py-2.5 glass rounded-xl text-red-400 text-sm font-semibold hover:bg-red-500/10 hover:border-red-500/30 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                </svg>
                Leave room
            </a>
        </div>
    </div>

    {{-- ── CENTER: Message stream ──────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0">

        {{-- Room header bar --}}
        <div class="flex items-center gap-4 px-6 py-3 border-b border-[#1e1e35] bg-[#07070f]/80 backdrop-blur-sm">
            <a href="{{ route('live.index') }}"
                class="p-1.5 rounded-xl hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors flex-shrink-0">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                </svg>
            </a>
            <div class="flex-1 min-w-0">
                <p class="font-display font-bold text-zinc-100 truncate">{{ $room->topic }}</p>
            </div>
            <div class="flex items-center gap-2 flex-shrink-0">
                <span class="flex items-center gap-1.5 px-3 py-1 bg-acid/10 border border-acid/25 rounded-full text-xs text-acid font-semibold">
                    <span class="live-dot w-1.5 h-1.5"></span>
                    <span id="header-participant-count">{{ $room->participant_count }}</span> live
                </span>
            </div>
        </div>

        {{-- Messages --}}
        <div class="flex-1 overflow-y-auto scrollbar-thin px-6 py-4 space-y-4" id="messages-container">
            @foreach($messages as $message)
            @include('partials.message-bubble', ['message' => $message])
            @endforeach
            <div id="messages-end"></div>
        </div>

        {{-- Message input --}}
        <div class="px-6 py-4 border-t border-[#1e1e35] bg-[#07070f]/80 backdrop-blur-sm">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-xl flex-shrink-0 object-cover ring-2 ring-[#1e1e35]">
                <div class="flex-1 flex items-center gap-3 glass rounded-2xl px-4 py-2.5">
                    <input type="text" id="message-input"
                        placeholder="Say something..."
                        class="flex-1 bg-transparent text-sm text-zinc-100 placeholder-zinc-600 focus:outline-none"
                        onkeydown="if(event.key==='Enter' && !event.shiftKey) { event.preventDefault(); sendMessage(); }">
                    @if($room->is_anonymous)
                    <label class="flex items-center gap-1.5 text-xs text-zinc-600 cursor-pointer hover:text-fuchsia-400 transition-colors flex-shrink-0">
                        <input type="checkbox" id="anon-msg-toggle" class="w-3 h-3 accent-fuchsia-500">
                        <span>anon</span>
                    </label>
                    @endif
                </div>
                <button onclick="sendMessage()"
                    class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 flex items-center justify-center transition-all hover:shadow-lg hover:shadow-violet-500/20 active:scale-95 flex-shrink-0">
                    <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const roomId  = {{ $room->id }};
const sendUrl = '{{ route('live.message', $room) }}';

function scrollToBottom() {
    const end = document.getElementById('messages-end');
    if (end) end.scrollIntoView({ behavior: 'smooth' });
}
scrollToBottom();

async function sendMessage() {
    const input = document.getElementById('message-input');
    const body  = input.value.trim();
    if (!body) return;

    const isAnon = document.getElementById('anon-msg-toggle')?.checked ?? false;
    input.value = '';

    try {
        await fetch(sendUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': window.csrfToken,
            },
            body: JSON.stringify({ body, is_anonymous: isAnon }),
        });
    } catch(e) {}
}

// Typing UI
const typingEl = document.createElement('div');
typingEl.id = 'typing-indicator';
typingEl.className = 'text-xs text-fuchsia-400 font-mono italic animate-pulse hidden px-6 py-1';
document.getElementById('messages-container').parentNode.insertBefore(typingEl, document.querySelector('.border-t.border-\\[\\#1e1e35\\]'));

let typingTimer;

document.getElementById('message-input').addEventListener('input', () => {
    if (window.Echo) {
        window.Echo.join(`live-room.${roomId}`).whisper('typing', {
            name: '{{ auth()->user()->display_name }}'
        });
    }
});

// WebSocket via Laravel Echo
document.addEventListener('DOMContentLoaded', () => {
    if (window.Echo) {
        window.Echo.join(`live-room.${roomId}`)
            .here((users) => {
                updateParticipants(users);
            })
            .joining((user) => {
                addParticipantUI(user);
            })
            .leaving((user) => {
                removeParticipantUI(user);
            })
            .listen('.new.message', (data) => {
                appendMessage(data);
                scrollToBottom();
            })
            .listenForWhisper('typing', (e) => {
                typingEl.textContent = `${e.name} is typing...`;
                typingEl.classList.remove('hidden');
                scrollToBottom();
                
                clearTimeout(typingTimer);
                typingTimer = setTimeout(() => {
                    typingEl.classList.add('hidden');
                }, 1500);
            });
    }
});

function updateParticipants(users) {
    document.getElementById('participant-count').textContent = users.length;
    document.getElementById('header-participant-count').textContent = users.length;
    
    const list = document.getElementById('participants-list');
    list.innerHTML = '';
    users.forEach(u => addParticipantUI(u));
}

function addParticipantUI(user) {
    const list = document.getElementById('participants-list');
    if (!document.getElementById(`participant-${user.id}`)) {
        list.insertAdjacentHTML('beforeend', `
            <div id="participant-${user.id}" class="flex items-center gap-2.5 px-2 py-2 rounded-xl hover:bg-[#0d0d1a] transition-colors">
                <div class="relative flex-shrink-0">
                    <img src="${user.avatar || 'https://api.dicebear.com/7.x/bottts/svg?seed='+user.id}" class="w-7 h-7 rounded-full object-cover">
                    <span class="absolute -bottom-0.5 -right-0.5 w-2 h-2 bg-acid rounded-full ring-1 ring-[#07070f]"></span>
                </div>
                <span class="text-xs text-zinc-400 truncate">${user.name}</span>
            </div>
        `);
    }
    updateCountUI(1);
}

function removeParticipantUI(user) {
    const el = document.getElementById(`participant-${user.id}`);
    if (el) el.remove();
    updateCountUI(-1);
}

function updateCountUI(delta) {
    const c1 = document.getElementById('participant-count');
    const c2 = document.getElementById('header-participant-count');
    const count = parseInt(c1.textContent) + delta;
    c1.textContent = count;
    c2.textContent = count;
}

function appendMessage(msg) {
    const container = document.getElementById('messages-container');
    const end = document.getElementById('messages-end');
    const div = document.createElement('div');
    div.className = 'flex items-start gap-3 animate-fade-in';
    div.innerHTML = `
        <img src="${msg.author_avatar}" class="w-8 h-8 rounded-xl flex-shrink-0 mt-0.5 object-cover ring-2 ring-[#1e1e35]">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs font-semibold text-zinc-300">${msg.author_name}</span>
                ${msg.is_anonymous ? '<span class="text-xs text-fuchsia-400 font-mono">anon</span>' : ''}
                <span class="text-xs text-zinc-700 font-mono">just now</span>
            </div>
            <div class="glass rounded-2xl rounded-tl-sm px-4 py-2.5 text-sm text-zinc-200 max-w-sm break-words">
                ${msg.body}
            </div>
        </div>
    `;
    container.insertBefore(div, end);
}
</script>
@endpush
