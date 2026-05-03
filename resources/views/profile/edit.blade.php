@extends('layouts.app')

@section('title', 'Edit Profile — OneTap')
@section('page-title', 'Edit Profile')

@section('content')
<div class="px-6 py-6 max-w-2xl">

    <form id="profile-form" enctype="multipart/form-data" class="space-y-6">
        @csrf

        {{-- ── Cover image ─────────────────────────────────────────────── --}}
        <div>
            <label class="panel-section-header block mb-3">Cover Image</label>
            <div class="relative h-40 rounded-2xl overflow-hidden glass group cursor-pointer">
                @if($user->cover_image)
                <img id="cover-preview" src="{{ $user->cover_image }}" class="w-full h-full object-cover">
                @else
                <div id="cover-preview-placeholder" class="w-full h-full bg-gradient-to-br from-violet-900/40 to-fuchsia-900/40 flex items-center justify-center">
                    <span class="text-zinc-600 text-sm">No cover image</span>
                </div>
                <img id="cover-preview" src="" class="w-full h-full object-cover hidden">
                @endif
                <label class="absolute inset-0 flex items-center justify-center bg-black/50 opacity-0 group-hover:opacity-100 cursor-pointer transition-opacity">
                    <div class="flex flex-col items-center gap-2 text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <span class="text-sm font-semibold">Upload cover</span>
                    </div>
                    <input type="file" name="cover_image" accept="image/*" class="hidden"
                           onchange="previewImage(this, 'cover-preview', 'cover-preview-placeholder')">
                </label>
            </div>
        </div>

        {{-- ── Avatar ──────────────────────────────────────────────────── --}}
        <div>
            <label class="panel-section-header block mb-3">Profile Photo</label>
            <div class="flex items-center gap-5">
                <div class="relative flex-shrink-0">
                    <img id="avatar-preview" src="{{ $user->avatar_url }}"
                        class="w-20 h-20 rounded-2xl object-cover ring-2 ring-[#1e1e35]">
                    <label class="absolute inset-0 flex items-center justify-center bg-black/60 rounded-2xl opacity-0 hover:opacity-100 cursor-pointer transition-opacity">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <input type="file" name="avatar" accept="image/*" class="hidden"
                               onchange="previewImage(this, 'avatar-preview')">
                    </label>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zinc-300 mb-1">Profile photo</p>
                    <p class="text-xs text-zinc-600">JPG, PNG, WebP — max 4MB</p>
                    <p class="text-xs text-zinc-700 mt-1">Hover to change</p>
                </div>
            </div>
        </div>

        {{-- ── Display name ────────────────────────────────────────────── --}}
        <div>
            <label class="panel-section-header block mb-2">Display Name</label>
            <input type="text" name="name" value="{{ $user->name }}"
                class="input-dark"
                placeholder="Your name">
        </div>

        {{-- ── Bio ──────────────────────────────────────────────────────── --}}
        <div>
            <label class="panel-section-header block mb-2">Bio</label>
            <textarea name="bio" rows="3"
                class="input-dark resize-none"
                placeholder="Tell your campus who you are...">{{ $user->bio }}</textarea>
        </div>

        {{-- ── Vibe tags ───────────────────────────────────────────────── --}}
        <div>
            <label class="panel-section-header block mb-2">Vibe Tags <span class="text-zinc-700 normal-case font-normal">(max 5)</span></label>
            <div class="flex gap-2 flex-wrap mb-3 min-h-[32px]" id="vibe-tags-display"></div>
            <div class="flex gap-2">
                <input type="text" id="vibe-tag-input" placeholder="Add a vibe tag..."
                    class="input-dark flex-1"
                    onkeydown="if(event.key==='Enter'){event.preventDefault();addVibeTag();}">
                <button type="button" onclick="addVibeTag()"
                    class="px-4 py-2.5 glass rounded-xl text-zinc-400 text-sm font-semibold hover:text-violet-300 hover:border-violet-500/30 transition-all flex-shrink-0">
                    Add
                </button>
            </div>
            <div id="vibe-tags-inputs"></div>
        </div>

        {{-- ── Campus ──────────────────────────────────────────────────── --}}
        @if(isset($campuses))
        <div>
            <label class="panel-section-header block mb-2">Campus</label>
            <select name="campus_id" class="input-dark">
                <option value="">No campus selected</option>
                @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ $user->campus_id == $campus->id ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
                @endforeach
            </select>
        </div>
        @endif

        {{-- ── Save button ─────────────────────────────────────────────── --}}
        <div class="flex items-center gap-4 pt-2">
            <button type="button" onclick="saveProfile()" class="btn-primary px-8 py-3">
                Save changes
            </button>
            <a href="{{ route('profile.show', $user) }}"
                class="text-sm text-zinc-600 hover:text-zinc-400 transition-colors">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
let vibeTags = @json($user->vibe_tags ?? []);

function previewImage(input, previewId, placeholderId = null) {
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            const preview = document.getElementById(previewId);
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            if (placeholderId) {
                const ph = document.getElementById(placeholderId);
                if (ph) ph.classList.add('hidden');
            }
        };
        reader.readAsDataURL(input.files[0]);
    }
}

function addVibeTag() {
    const input = document.getElementById('vibe-tag-input');
    const tag = input.value.trim().toLowerCase().replace(/[^a-z0-9_]/g, '');
    if (!tag || vibeTags.includes(tag) || vibeTags.length >= 5) return;
    vibeTags.push(tag);
    renderVibeTags();
    input.value = '';
}

function removeVibeTag(tag) {
    vibeTags = vibeTags.filter(t => t !== tag);
    renderVibeTags();
}

function renderVibeTags() {
    const display = document.getElementById('vibe-tags-display');
    const inputs  = document.getElementById('vibe-tags-inputs');
    display.innerHTML = vibeTags.map(tag => `
        <span class="flex items-center gap-1.5 px-3 py-1.5 bg-[#0d0d1a] border border-[#1e1e35] text-zinc-400 text-xs rounded-full">
            <span class="text-violet-500">#</span>${tag}
            <button type="button" onclick="removeVibeTag('${tag}')"
                class="text-zinc-700 hover:text-red-400 transition-colors ml-0.5 text-sm leading-none">×</button>
        </span>
    `).join('');
    inputs.innerHTML = vibeTags.map(tag => `<input type="hidden" name="vibe_tags[]" value="${tag}">`).join('');
}

async function saveProfile() {
    renderVibeTags();
    const form = document.getElementById('profile-form');
    const formData = new FormData(form);

    const btn = document.querySelector('[onclick="saveProfile()"]');
    btn.textContent = 'Saving...';
    btn.disabled = true;

    try {
        const res = await fetch('{{ route('profile.update') }}', {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': window.csrfToken },
            body: formData,
        });
        const data = await res.json();
        if (data.success) {
            window.location.href = '{{ route('profile.show', $user) }}';
        } else {
            btn.textContent = 'Save changes';
            btn.disabled = false;
        }
    } catch(e) {
        btn.textContent = 'Save changes';
        btn.disabled = false;
        alert('Failed to save. Try again.');
    }
}

// Init
renderVibeTags();
</script>
@endpush
