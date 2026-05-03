<!DOCTYPE html>
<html lang="en" class="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'OneTap — What\'s happening around you')</title>
    <meta name="description" content="@yield('meta_description', 'Hyperlocal social for Gen-Z. Real moments. Real people. Right now.')">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>
<body class="bg-[#030305] text-zinc-100 font-sans antialiased overflow-x-hidden">

    {{-- ── Ambient background ──────────────────────────────────────────── --}}
    <div class="fixed inset-0 pointer-events-none z-0 overflow-hidden">
        {{-- Grid --}}
        <div class="ambient-grid absolute inset-0 opacity-60"></div>
        {{-- Gradient orbs --}}
        <div class="absolute -top-60 -left-60 w-[500px] h-[500px] bg-violet-600/8 rounded-full blur-3xl animate-orb-drift"></div>
        <div class="absolute -bottom-60 -right-60 w-[500px] h-[500px] bg-fuchsia-600/8 rounded-full blur-3xl animate-orb-drift" style="animation-delay: -4s;"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[700px] h-[700px] bg-cyan-600/4 rounded-full blur-3xl animate-orb-drift" style="animation-delay: -8s;"></div>
    </div>

    @auth
    {{-- ════════════════════════════════════════════════════════════════════
         AUTHENTICATED — 3-COLUMN DESKTOP LAYOUT
    ════════════════════════════════════════════════════════════════════ --}}

    {{-- ── LEFT RAIL ──────────────────────────────────────────────────── --}}
    <aside class="fixed left-0 top-0 h-screen w-[280px] z-40 flex flex-col border-r border-[#1e1e35] bg-[#07070f]/95 backdrop-blur-xl">

        {{-- Logo --}}
        <div class="flex items-center gap-3 px-6 py-5 border-b border-[#1e1e35]">
            <a href="{{ route('feed.index') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-fuchsia-500 flex items-center justify-center shadow-lg glow-violet flex-shrink-0 group-hover:scale-105 transition-transform">
                    <img src="/OneTap.png" alt="OT" class="w-6 h-6 object-contain" onerror="this.style.display='none';this.nextElementSibling.style.display='flex'">
                    <span class="text-white font-black text-sm hidden items-center justify-center">OT</span>
                </div>
                <div>
                    <span class="font-display font-black text-white text-xl tracking-tight leading-none">OneTap</span>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="live-dot w-1.5 h-1.5"></span>
                        <span class="text-[10px] text-zinc-500 font-mono" id="rail-online-count">— online</span>
                    </div>
                </div>
            </a>
        </div>

        {{-- Navigation --}}
        <nav class="flex-1 py-4 overflow-y-auto scrollbar-none">
            <a href="{{ route('feed.index') }}"
               class="nav-rail-item {{ request()->routeIs('feed.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Feed</span>
            </a>

            <a href="{{ route('confessions.index') }}"
               class="nav-rail-item {{ request()->routeIs('confessions.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                </svg>
                <span>Confessions</span>
            </a>

            <a href="{{ route('events.index') }}"
               class="nav-rail-item {{ request()->routeIs('events.*') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                <span>Events</span>
            </a>

            <a href="{{ route('live.index') }}"
               class="nav-rail-item {{ request()->routeIs('live.*') ? 'active' : '' }}">
                <span class="relative flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0"/>
                    </svg>
                    <span class="absolute -top-1 -right-1 w-2 h-2 bg-acid rounded-full animate-pulse"></span>
                </span>
                <span>Live Rooms</span>
            </a>

            <a href="{{ route('notifications.index') }}"
               class="nav-rail-item {{ request()->routeIs('notifications.*') ? 'active' : '' }}">
                <span class="relative flex-shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span id="notif-badge-rail" class="absolute -top-1 -right-1 w-2 h-2 bg-fuchsia-500 rounded-full hidden animate-pulse"></span>
                </span>
                <span>Notifications</span>
            </a>

            <a href="{{ route('profile.show', auth()->user()) }}"
               class="nav-rail-item {{ request()->routeIs('profile.show') && request()->route('user')?->id === auth()->id() ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                <span>Profile</span>
            </a>

            <a href="{{ route('profile.edit') }}"
               class="nav-rail-item {{ request()->routeIs('profile.edit') ? 'active' : '' }}">
                <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
            </a>
        </nav>

        {{-- User identity card --}}
        <div class="border-t border-[#1e1e35] p-4">
            <div class="glass rounded-2xl p-3">
                <div class="flex items-center gap-3 mb-3">
                    <div class="relative flex-shrink-0">
                        <img src="{{ auth()->user()->avatar_url }}" alt="avatar"
                             class="w-10 h-10 rounded-xl object-cover ring-2 ring-violet-500/30">
                        <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 bg-acid rounded-full ring-2 ring-[#07070f]"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-zinc-100 truncate">{{ auth()->user()->display_name }}</p>
                        <p class="text-xs text-zinc-500 truncate">@{{ auth()->user()->username }}</p>
                    </div>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="p-1.5 rounded-lg hover:bg-zinc-800 text-zinc-600 hover:text-zinc-400 transition-colors" title="Logout">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </button>
                    </form>
                </div>
                {{-- Social energy stats --}}
                <div class="grid grid-cols-3 gap-1.5">
                    <div class="text-center bg-[#0d0d1a] rounded-xl py-1.5">
                        <div class="text-xs font-mono font-bold text-violet-400">{{ auth()->user()->chaos_score ?? 0 }}</div>
                        <div class="text-[9px] text-zinc-600 uppercase tracking-wider">Chaos</div>
                    </div>
                    <div class="text-center bg-[#0d0d1a] rounded-xl py-1.5">
                        <div class="text-xs font-mono font-bold text-fuchsia-400">{{ auth()->user()->mystery_score ?? 0 }}</div>
                        <div class="text-[9px] text-zinc-600 uppercase tracking-wider">Mystery</div>
                    </div>
                    <div class="text-center bg-[#0d0d1a] rounded-xl py-1.5">
                        <div class="text-xs font-mono font-bold text-cyan-400">{{ auth()->user()->streak?->current_streak ?? 0 }}</div>
                        <div class="text-[9px] text-zinc-600 uppercase tracking-wider">Streak</div>
                    </div>
                </div>
            </div>
        </div>
    </aside>

    {{-- ── MAIN CONTENT ────────────────────────────────────────────────── --}}
    <main class="ml-[280px] mr-[320px] min-h-screen relative z-10">

        {{-- Top bar --}}
        <header class="sticky top-0 z-30 bg-[#030305]/80 backdrop-blur-xl border-b border-[#1e1e35]">
            <div class="flex items-center justify-between px-6 h-14">
                <div class="flex items-center gap-3">
                    <h2 class="font-display font-bold text-zinc-100 text-base">@yield('page-title', 'Feed')</h2>
                    @yield('page-subtitle')
                </div>
                <div class="flex items-center gap-3">
                    @yield('header-actions')
                    {{-- Search --}}
                    <div class="relative hidden lg:block">
                        <input type="text" placeholder="Search OneTap..."
                            class="w-52 bg-[#0d0d1a] border border-[#1e1e35] rounded-xl px-4 py-2 pl-9 text-sm text-zinc-300 placeholder-zinc-600 focus:outline-none focus:border-violet-500/50 transition-colors">
                        <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-zinc-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    {{-- New post button --}}
                    <button onclick="document.getElementById('post-modal').classList.remove('hidden')"
                        class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white text-sm font-bold rounded-xl transition-all hover:shadow-lg hover:shadow-violet-500/20 active:scale-95">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span>Drop</span>
                    </button>
                </div>
            </div>
        </header>

        {{-- Flash messages --}}
        @if(session('success'))
        <div class="mx-6 mt-4 px-4 py-3 bg-acid/10 border border-acid/30 rounded-xl text-acid text-sm animate-slide-up">
            {{ session('success') }}
        </div>
        @endif
        @if($errors->any())
        <div class="mx-6 mt-4 px-4 py-3 bg-red-500/10 border border-red-500/30 rounded-xl text-red-400 text-sm animate-slide-up">
            {{ $errors->first() }}
        </div>
        @endif

        @yield('content')
    </main>

    {{-- ── RIGHT PANEL ─────────────────────────────────────────────────── --}}
    <aside class="fixed right-0 top-0 h-screen w-[320px] z-40 flex flex-col border-l border-[#1e1e35] bg-[#07070f]/95 backdrop-blur-xl overflow-y-auto scrollbar-thin">

        {{-- Live activity header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-[#1e1e35]">
            <div class="flex items-center gap-2">
                <span class="live-dot"></span>
                <span class="font-display font-bold text-sm text-zinc-100">Live Activity</span>
            </div>
            <span class="text-[10px] font-mono text-zinc-600 uppercase tracking-wider">Now</span>
        </div>

        {{-- Online count + campus --}}
        <div class="px-5 py-4 border-b border-[#1e1e35]">
            <div class="glass rounded-2xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <div class="text-2xl font-display font-black text-acid" id="panel-online-count">—</div>
                        <div class="text-xs text-zinc-500">students online now</div>
                    </div>
                    <div class="w-10 h-10 rounded-xl bg-acid/10 border border-acid/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-acid" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                </div>
                @auth
                @if(auth()->user()->campus)
                <div class="flex items-center gap-2 text-xs text-zinc-500">
                    <span class="text-violet-400">🏫</span>
                    <span>{{ auth()->user()->campus->name }}</span>
                </div>
                @endif
                @endauth
            </div>
        </div>

        {{-- Chaos meter --}}
        <div class="px-5 py-4 border-b border-[#1e1e35]">
            <div class="panel-section-header">Campus Chaos Meter</div>
            <div class="glass rounded-2xl p-4">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs text-zinc-400 font-medium">⚡ Energy Level</span>
                    <span class="text-xs font-mono text-ember" id="chaos-pct">—%</span>
                </div>
                <div class="h-2 bg-[#0d0d1a] rounded-full overflow-hidden">
                    <div class="h-full rounded-full bg-gradient-to-r from-violet-500 via-fuchsia-500 to-ember animate-chaos-bar"
                         style="width: 60%"></div>
                </div>
                <div class="flex justify-between mt-1.5">
                    <span class="text-[10px] text-zinc-700">Chill</span>
                    <span class="text-[10px] text-zinc-700">Chaos</span>
                </div>
            </div>
        </div>

        {{-- Trending topics --}}
        <div class="px-5 py-4 border-b border-[#1e1e35]">
            <div class="panel-section-header">Trending</div>
            <div class="space-y-1" id="trending-panel">
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#0d0d1a] transition-colors cursor-pointer group">
                    <span class="text-xs font-mono text-zinc-700 w-4">1</span>
                    <div class="flex-1 min-w-0">
                        <div class="text-sm text-zinc-300 font-medium group-hover:text-violet-400 transition-colors truncate">#loading</div>
                        <div class="text-xs text-zinc-600">— posts</div>
                    </div>
                    <span class="text-ember text-xs">🔥</span>
                </div>
            </div>
        </div>

        {{-- Active users --}}
        <div class="px-5 py-4 border-b border-[#1e1e35]">
            <div class="panel-section-header">Active Now</div>
            <div class="space-y-2" id="active-users-panel">
                <div class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-[#0d0d1a] transition-colors">
                    <div class="relative flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-[#1e1e35] animate-pulse"></div>
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-acid rounded-full ring-2 ring-[#07070f]"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-zinc-400 truncate">Loading...</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Quick post button --}}
        <div class="p-5 mt-auto">
            <button onclick="document.getElementById('post-modal').classList.remove('hidden')"
                class="w-full py-3 bg-gradient-to-r from-violet-600 to-fuchsia-600 hover:from-violet-500 hover:to-fuchsia-500 text-white font-bold text-sm rounded-2xl transition-all hover:shadow-lg hover:shadow-violet-500/20 active:scale-[0.98] flex items-center justify-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Drop something
            </button>
        </div>
    </aside>

    {{-- Post creation modal --}}
    @include('partials.post-modal')

    @else
    {{-- ════════════════════════════════════════════════════════════════════
         GUEST — FULL WIDTH
    ════════════════════════════════════════════════════════════════════ --}}
    <main class="min-h-screen relative z-10">
        @yield('content')
    </main>
    @endauth

    @stack('scripts')

    <script>
        window.csrfToken = '{{ csrf_token() }}';

        @auth
        // Notification badge polling
        async function checkNotifications() {
            try {
                const res = await fetch('{{ route('notifications.count') }}');
                const data = await res.json();
                const badge = document.getElementById('notif-badge-rail');
                if (badge) badge.classList.toggle('hidden', data.count === 0);
            } catch(e) {}
        }
        checkNotifications();
        // setInterval(checkNotifications, 30000); // Will replace with Echo listener for Notifications soon

        // Realtime Online Presence
        const userCampusId = '{{ auth()->user()->campus_id ?? 'global' }}';
        
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                window.Echo.join(`campus.${userCampusId}`)
                    .here((users) => {
                        updateOnlineCountUI(users.length);
                        renderActiveUsers(users);
                    })
                    .joining((user) => {
                        let el = document.getElementById('panel-online-count');
                        let count = el && el.textContent !== '—' ? parseInt(el.textContent) : 0;
                        updateOnlineCountUI(count + 1);
                        addActiveUser(user);
                    })
                    .leaving((user) => {
                        let el = document.getElementById('panel-online-count');
                        let count = el && el.textContent !== '—' ? parseInt(el.textContent) : 1;
                        updateOnlineCountUI(Math.max(0, count - 1));
                        removeActiveUser(user);
                    })
                    .listen('ChaosLevelUpdated', (e) => {
                        updateChaosUI(e.percentage);
                    });
            }
        });

        function updateOnlineCountUI(count) {
            const el1 = document.getElementById('panel-online-count');
            const el2 = document.getElementById('rail-online-count');
            if (el1) el1.textContent = count;
            if (el2) el2.textContent = count + ' online';
        }

        function renderActiveUsers(users) {
            const panel = document.getElementById('active-users-panel');
            if (!panel) return;
            panel.innerHTML = '';
            users.slice(0, 8).forEach(u => addActiveUser(u, true));
            if (users.length === 0) {
                panel.innerHTML = '<div class="text-xs text-zinc-600 px-2 py-1.5">No one is active right now.</div>';
            }
        }
        
        function addActiveUser(user, append = false) {
            const panel = document.getElementById('active-users-panel');
            if (!panel) return;
            // remove placeholder or empty state
            if (panel.innerHTML.includes('Loading...') || panel.innerHTML.includes('No one')) {
                panel.innerHTML = '';
            }
            if (document.getElementById(`active-user-${user.id}`)) return;
            
            const html = `
                <div id="active-user-${user.id}" class="flex items-center gap-3 px-2 py-1.5 rounded-xl hover:bg-[#0d0d1a] transition-colors animate-fade-in">
                    <div class="relative flex-shrink-0">
                        <img src="${user.avatar || '/default-avatar.png'}" class="w-8 h-8 rounded-full object-cover">
                        <span class="absolute -bottom-0.5 -right-0.5 w-2.5 h-2.5 bg-acid rounded-full ring-2 ring-[#07070f]"></span>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="text-xs text-zinc-300 font-semibold truncate">${user.name}</div>
                        <div class="text-[10px] text-zinc-500">Active</div>
                    </div>
                </div>`;
            if (append) {
                panel.insertAdjacentHTML('beforeend', html);
            } else {
                panel.insertAdjacentHTML('afterbegin', html);
            }
        }
        
        function removeActiveUser(user) {
            const el = document.getElementById(`active-user-${user.id}`);
            if (el) el.remove();
        }

        function updateChaosUI(pct) {
            const cpct = document.getElementById('chaos-pct');
            if (cpct) cpct.textContent = pct + '%';
            const bar = document.querySelector('.animate-chaos-bar');
            if (bar) bar.style.width = pct + '%';
        }

        // Fetch initial Chaos PCT & Trending
        async function fetchInitialStats() {
            try {
                const res = await fetch('/live/stats');
                const data = await res.json();
                updateChaosUI(data.chaos_pct);
                
                // Render trending
                const tPanel = document.getElementById('trending-panel');
                if (tPanel && data.trending) {
                    tPanel.innerHTML = '';
                    if (data.trending.length === 0) {
                        tPanel.innerHTML = '<div class="text-xs text-zinc-600 px-3 py-2">Nothing trending yet.</div>';
                    }
                    data.trending.forEach((t, index) => {
                        tPanel.insertAdjacentHTML('beforeend', `
                            <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl hover:bg-[#0d0d1a] transition-colors cursor-pointer group">
                                <span class="text-xs font-mono text-zinc-700 w-4">${index + 1}</span>
                                <div class="flex-1 min-w-0">
                                    <div class="text-sm text-zinc-300 font-medium group-hover:text-violet-400 transition-colors truncate">#${t.topic}</div>
                                    <div class="text-xs text-zinc-600">${t.post_count} posts</div>
                                </div>
                                <span class="text-ember text-xs">🔥</span>
                            </div>
                        `);
                    });
                }
            } catch(e) {}
        }
        fetchInitialStats();
        
        // Listen for new notifications
        document.addEventListener('DOMContentLoaded', () => {
            if (window.Echo) {
                window.Echo.private(`App.Models.User.{{ auth()->id() }}`)
                    .notification((notification) => {
                        checkNotifications();
                        // Optionally show a toast notification here
                    });
            }
        });
        @endauth
    </script>
</body>
</html>
