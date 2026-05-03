{{-- Post creation modal — centered full-screen overlay (NOT bottom sheet) --}}
<div id="post-modal" class="hidden fixed inset-0 z-[100] flex items-center justify-center p-4"
     x-data="{ type: 'text' }">

    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-black/80 backdrop-blur-md"
         onclick="document.getElementById('post-modal').classList.add('hidden')"></div>

    {{-- Modal panel --}}
    <div class="relative w-full max-w-2xl glass-dark rounded-3xl border border-[#1e1e35] shadow-2xl shadow-violet-500/5 animate-slide-up overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-6 py-4 border-b border-[#1e1e35]">
            <div class="flex items-center gap-3">
                <img src="{{ auth()->user()->avatar_url }}" class="w-9 h-9 rounded-xl object-cover ring-2 ring-[#1e1e35]">
                <div>
                    <p class="text-sm font-semibold text-zinc-200">{{ auth()->user()->display_name }}</p>
                    <p class="text-xs text-zinc-600">@{{ auth()->user()->username }}</p>
                </div>
            </div>
            <button onclick="document.getElementById('post-modal').classList.add('hidden')"
                class="p-2 rounded-xl hover:bg-[#1e1e35] text-zinc-600 hover:text-zinc-400 transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        {{-- Type selector --}}
        <div class="flex gap-1.5 overflow-x-auto px-6 py-3 border-b border-[#1e1e35] scrollbar-none">
            @foreach([
                ['text',      '💬', 'Thought'],
                ['image',     '📸', 'Photo'],
                ['poll',      '📊', 'Poll'],
                ['spotted',   '👀', 'Spotted'],
                ['question',  '❓', 'Question'],
                ['hot_take',  '🔥', 'Hot Take'],
                ['challenge', '⚡', 'Challenge'],
            ] as [$t, $emoji, $label])
            <button type="button"
                onclick="selectPostType('{{ $t }}')"
                id="type-btn-{{ $t }}"
                class="post-type-btn flex-shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold border border-[#1e1e35] text-zinc-500 hover:border-violet-500/30 hover:text-violet-400 transition-all">
                {{ $emoji }} {{ $label }}
            </button>
            @endforeach
        </div>

        {{-- Form body --}}
        <form id="post-form" action="{{ route('posts.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="type" id="post-type-input" value="text">

            <div class="px-6 py-4">
                {{-- Main textarea --}}
                <textarea name="body" id="post-body"
                    placeholder="What's happening around you? 👀"
                    rows="5"
                    class="w-full bg-transparent text-zinc-100 placeholder-zinc-600 text-base leading-relaxed resize-none focus:outline-none"></textarea>

                {{-- Image upload --}}
                <div id="media-section" class="hidden mt-3">
                    <label class="flex items-center gap-3 px-4 py-4 bg-[#0d0d1a] border border-dashed border-[#1e1e35] rounded-2xl cursor-pointer hover:border-violet-500/40 hover:bg-violet-500/5 transition-all group">
                        <div class="w-10 h-10 rounded-xl bg-[#1e1e35] flex items-center justify-center flex-shrink-0 group-hover:bg-violet-500/10 transition-colors">
                            <svg class="w-5 h-5 text-zinc-500 group-hover:text-violet-400 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm text-zinc-400 font-medium group-hover:text-violet-300 transition-colors">Drop photos here or click to upload</p>
                            <p class="text-xs text-zinc-700 mt-0.5">JPG, PNG, WebP — max 10MB each</p>
                        </div>
                        <input type="file" name="media[]" accept="image/*" class="hidden" multiple>
                    </label>
                </div>

                {{-- Poll builder --}}
                <div id="poll-section" class="hidden mt-3 space-y-3">
                    <input type="text" name="poll_question" placeholder="Ask your campus something..."
                        class="input-dark">
                    <div id="poll-options-container" class="space-y-2">
                        <input type="text" name="poll_options[]" placeholder="Option 1"
                            class="poll-option input-dark">
                        <input type="text" name="poll_options[]" placeholder="Option 2"
                            class="poll-option input-dark">
                    </div>
                    <button type="button" onclick="addPollOption()"
                        class="text-violet-400 text-sm font-semibold hover:text-violet-300 transition-colors flex items-center gap-1">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        Add option
                    </button>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-6 py-4 border-t border-[#1e1e35]">
                <label class="flex items-center gap-2.5 cursor-pointer group">
                    <div class="relative flex-shrink-0">
                        <input type="checkbox" name="is_anonymous" id="anon-toggle" class="sr-only peer">
                        <div class="w-10 h-5 bg-[#1e1e35] rounded-full peer-checked:bg-violet-600 transition-colors"></div>
                        <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-zinc-500 rounded-full transition-transform peer-checked:translate-x-5 peer-checked:bg-white"></div>
                    </div>
                    <span class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors">Post anonymously</span>
                </label>

                <button type="submit"
                    class="btn-primary px-6 py-2.5 text-sm">
                    Drop it 🔥
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function selectPostType(type) {
    document.getElementById('post-type-input').value = type;

    document.querySelectorAll('.post-type-btn').forEach(btn => {
        btn.classList.remove('border-violet-500/40', 'text-violet-400', 'bg-violet-500/10');
        btn.classList.add('border-[#1e1e35]', 'text-zinc-500');
    });
    const active = document.getElementById('type-btn-' + type);
    if (active) {
        active.classList.remove('border-[#1e1e35]', 'text-zinc-500');
        active.classList.add('border-violet-500/40', 'text-violet-400', 'bg-violet-500/10');
    }

    document.getElementById('media-section').classList.toggle('hidden', !['image', 'meme'].includes(type));
    document.getElementById('poll-section').classList.toggle('hidden', type !== 'poll');

    const placeholders = {
        text:      "What's happening around you? 👀",
        image:     "Caption this moment...",
        spotted:   "Spotted someone? Describe them... 👀",
        question:  "Ask your campus anything...",
        hot_take:  "Drop your hottest take 🔥",
        challenge: "Issue a challenge to your campus ⚡",
        poll:      "What do you want to know?",
    };
    document.getElementById('post-body').placeholder = placeholders[type] || "What's on your mind?";
}

function addPollOption() {
    const container = document.getElementById('poll-options-container');
    const count = container.querySelectorAll('.poll-option').length;
    if (count >= 6) return;
    const input = document.createElement('input');
    input.type = 'text';
    input.name = 'poll_options[]';
    input.placeholder = `Option ${count + 1}`;
    input.className = 'poll-option input-dark';
    container.appendChild(input);
}

// Init
selectPostType('text');
</script>
