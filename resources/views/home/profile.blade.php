@extends('layouts.base')

@section('title', $user->username . "'s - OneTap")
@section('header-title', $user->name ?? $user->username)
@section('header-subtitle', '@' . $user->username)

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #0f0f23, #1a1a2e, #16213e, #0f3460);
        background-size: 400% 400%;
        animation: gradientShift 15s ease infinite;
        color: #ffffff;
        font-family: 'Inter', 'Poppins', sans-serif;
        min-height: 100vh;
    }

    @keyframes gradientShift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .profile-container {
        max-width: 900px;
        margin: 2rem auto;
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 24px;
        padding: 2rem;
        box-shadow: 
            0 8px 32px rgba(0, 0, 0, 0.3),
            inset 0 1px 0 rgba(255, 255, 255, 0.1);
        position: relative;
        overflow: hidden;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .profile-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.05), transparent);
        transition: left 0.5s;
    }

    .profile-container:hover::before {
        left: 100%;
    }

    .profile-header {
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
    }

    .avatar-container {
        position: relative;
        width: 140px;
        height: 140px;
        margin: 0 auto 1.5rem;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid transparent;
        background: linear-gradient(45deg, #ff6f61, #de4c4f, #ff6f61);
        padding: 3px;
        box-shadow: 
            0 0 30px rgba(255, 111, 97, 0.4),
            0 8px 16px rgba(0, 0, 0, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .avatar-container:hover {
        transform: scale(1.05);
        box-shadow: 
            0 0 40px rgba(255, 111, 97, 0.6),
            0 12px 24px rgba(0, 0, 0, 0.4);
    }

    .avatar-inner {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        overflow: hidden;
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .avatar-upload-overlay {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.7);
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        backdrop-filter: blur(10px);
    }

    .avatar-container:hover .avatar-upload-overlay {
        opacity: 1;
    }

    .user-info h1 {
        font-size: 2.5rem;
        font-weight: 700;
        background: linear-gradient(45deg, #ff6f61, #de4c4f, #ff8a65);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.5rem;
        letter-spacing: -0.02em;
    }

    .user-info .username {
        color: #94a3b8;
        font-size: 1.1rem;
        font-weight: 500;
        margin-bottom: 1rem;
    }

    .verification-badge {
        display: inline-flex;
        align-items: center;
        background: linear-gradient(45deg, #3b82f6, #1d4ed8);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 500;
        margin-left: 0.5rem;
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .form-section {
        background: rgba(255, 255, 255, 0.03);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        transition: all 0.3s ease;
    }

    .form-section:hover {
        background: rgba(255, 255, 255, 0.05);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .input-glass {
        background: rgba(255, 255, 255, 0.08);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 1rem;
        color: #ffffff;
        font-size: 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        width: 100%;
    }

    .input-glass:focus {
        border-color: #ff6f61;
        outline: none;
        background: rgba(255, 255, 255, 0.12);
        box-shadow: 0 0 0 3px rgba(255, 111, 97, 0.1);
    }

    .input-glass::placeholder {
        color: #94a3b8;
    }

    .label {
        display: block;
        color: #e2e8f0;
        font-weight: 600;
        margin-bottom: 0.5rem;
        font-size: 0.95rem;
        letter-spacing: 0.025em;
    }

    .button {
        background: linear-gradient(135deg, #ff6f61, #de4c4f);
        border: none;
        border-radius: 12px;
        padding: 0.875rem 1.5rem;
        color: white;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.95rem;
        position: relative;
        overflow: hidden;
    }

    .button:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(255, 111, 97, 0.4);
    }

    .button:active {
        transform: translateY(0);
    }

    .button-secondary {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .button-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
        box-shadow: 0 8px 25px rgba(255, 255, 255, 0.1);
    }

    .stats {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin: 2rem 0;
    }

    .stat-item {
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }

    .stat-item:hover {
        transform: translateY(-4px);
        background: rgba(255, 255, 255, 0.08);
        border-color: rgba(255, 255, 255, 0.2);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 700;
        background: linear-gradient(45deg, #ff6f61, #de4c4f);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        margin-bottom: 0.25rem;
    }

    .stat-label {
        color: #94a3b8;
        font-weight: 500;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
    }

    .action-buttons {
        display: flex;
        justify-content: center;
        gap: 1rem;
        margin: 2rem 0;
        flex-wrap: wrap;
    }

    .moments-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 2rem;
    }

    .moment-card {
        position: relative;
        border-radius: 16px;
        overflow: hidden;
        aspect-ratio: 1;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }

    .moment-card:hover {
        transform: scale(1.05);
        box-shadow: 0 12px 30px rgba(0, 0, 0, 0.3);
    }

    .moment-card img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: all 0.3s ease;
    }

    .moment-card:hover img {
        transform: scale(1.1);
        filter: brightness(1.1);
    }

    .toast {
        position: fixed;
        top: 2rem;
        right: 2rem;
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        box-shadow: 0 8px 25px rgba(16, 185, 129, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        transform: translateX(100%);
        z-index: 1000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .toast.show {
        transform: translateX(0);
    }

    .floating-save {
        position: fixed;
        bottom: 2rem;
        right: 2rem;
        z-index: 100;
        opacity: 0;
        transform: scale(0.8);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .floating-save.show {
        opacity: 1;
        transform: scale(1);
    }

    .profile-container.editing .floating-save {
        opacity: 1;
        transform: scale(1);
    }

    .status-indicator {
        position: absolute;
        bottom: 8px;
        right: 8px;
        width: 24px;
        height: 24px;
        background: #10b981;
        border: 3px solid white;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.7); }
        70% { box-shadow: 0 0 0 10px rgba(16, 185, 129, 0); }
        100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0); }
    }

    .section-title {
        font-size: 1.5rem;
        font-weight: 700;
        color: #e2e8f0;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    @media (max-width: 768px) {
        .profile-container {
            margin: 1rem;
            padding: 1.5rem;
        }
        
        .stats {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .action-buttons {
            flex-direction: column;
            align-items: center;
        }
        
        .moments-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="profile-container">
    <form id="profileForm" method="POST" action="{{ route('profile.update', $user->username) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="profile-header">
            <div class="avatar-container">
                <div class="avatar-inner">
                    @if($user->avatar)
                        <img id="avatarDisplay" src="{{ $user->avatar }}" alt="Avatar" class="w-full h-full object-cover" />
                    @else
                        <span id="avatarLetter" class="text-6xl font-extrabold text-transparent bg-clip-text bg-gradient-to-br from-pink-400 via-purple-500 to-indigo-400">{{ strtoupper(substr($user->username, 0, 1)) }}</span>
                    @endif
                    <div class="status-indicator"></div>
                </div>
                <div class="avatar-upload-overlay">
                    <svg class="w-8 h-8 text-white mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <span class="text-xs">Change Photo</span>
                </div>
                <input type="file" id="avatarInput" name="avatar" accept="image/*" class="hidden">
            </div>

            <div class="user-info">
                @if($user->verified ?? false)
                    <span class="verification-badge">
                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                        </svg>
                        Verified
                    </span>
                @endif
                <p class="username">{{ '@' . $user->username }}</p>
            </div>
        </div>

        <div class="form-section">
            <input type="text" name="name" value="{{ $user->name }}" class="input-glass" placeholder="Your awesome display name">
        </div>

        <div class="form-section">
            <label class="label">📝 Bio</label>
            <textarea name="bio" rows="2" class="input-glass" placeholder="Tell the world your story... What makes you unique?">{{ $user->bio }}</textarea>
        </div>

        <div class="stats">
            <div class="stat-item">
                <div class="stat-value">🔥 {{ $user->streak ?? 0 }}</div>
                <div class="stat-label">Day Streak</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">📸 {{ $user->posts_count ?? 0 }}</div>
                <div class="stat-label">Moments</div>
            </div>
            <div class="stat-item">
                <div class="stat-value">🤝 {{ $user->friends_count ?? 0 }}</div>
                <div class="stat-label">Connections</div>
            </div>
        </div>

        <div class="action-buttons">
            @auth
                @if(auth()->id() !== $user->id)
                    <button type="button" class="button">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Follow
                    </button>
                    <button type="button" class="button button-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Message
                    </button>
                @else
                    <button type="button" id="editBtn" class="button">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Profile
                    </button>
                    <button type="button" id="cancelBtn" class="button button-secondary" style="display: none;">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                        Cancel
                    </button>
                @endif
            @else
                <a href="{{ route('login') }}" class="button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    Login to Follow
                </a>
            @endauth
        </div>
    </form>

    @auth
        @if(auth()->id() === $user->id)
            <div class="floating-save" id="floatingSave">
                <button type="submit" form="profileForm" class="button">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    Save Changes
                </button>
            </div>
        @endif
    @endauth

    @if(isset($moments) && $moments->count())
        <div class="mt-8">
            <h3 class="section-title">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
                Shared Moments
            </h3>
            <div class="moments-grid">
                @foreach($moments as $moment)
                    <div class="moment-card">
                        <img src="{{ $moment->photo_url }}" alt="Moment" loading="lazy" />
                    </div>
                @endforeach
            </div>
            @if($moments->count() >= 6)
                <div class="text-center mt-6">
                    <button class="button button-secondary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        View All Moments
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>

<div id="toast" class="toast">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
    </svg>
    <span>Profile updated successfully!</span>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.querySelector('.profile-container');
    const editBtn = document.getElementById('editBtn');
    const cancelBtn = document.getElementById('cancelBtn');
    const avatarInput = document.getElementById('avatarInput');
    const avatarDisplay = document.getElementById('avatarDisplay');
    const avatarLetter = document.getElementById('avatarLetter');
    const avatarContainer = document.querySelector('.avatar-container');
    const form = document.getElementById('profileForm');
    const floatingSave = document.getElementById('floatingSave');
    
    let originalValues = {};
    let isEditing = false;

    // Avatar upload functionality
    if (avatarContainer) {
        avatarContainer.addEventListener('click', function() {
            if (isEditing && avatarInput) {
                avatarInput.click();
            }
        });
    }

    if (editBtn) {
        editBtn.addEventListener('click', function() {
            const nameInput = form.querySelector('input[name="name"]');
            const bioInput = form.querySelector('textarea[name="bio"]');
            
            originalValues = {
                name: nameInput.value,
                bio: bioInput.value
            };
            
            isEditing = true;
            container.classList.add('editing');
            editBtn.style.display = 'none';
            cancelBtn.style.display = 'inline-flex';
            
            // Enable form inputs
            nameInput.disabled = false;
            bioInput.disabled = false;
            
            // Show floating save button
            if (floatingSave) {
                floatingSave.classList.add('show');
            }
        });
    }

    if (cancelBtn) {
        cancelBtn.addEventListener('click', function() {
            const nameInput = form.querySelector('input[name="name"]');
            const bioInput = form.querySelector('textarea[name="bio"]');
            
            nameInput.value = originalValues.name;
            bioInput.value = originalValues.bio;
            
            isEditing = false;
            container.classList.remove('editing');
            editBtn.style.display = 'inline-flex';
            cancelBtn.style.display = 'none';
            
            // Disable form inputs
            nameInput.disabled = true;
            bioInput.disabled = true;
            
            // Hide floating save button
            if (floatingSave) {
                floatingSave.classList.remove('show');
            }
        });
    }

    if (avatarInput) {
        avatarInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (avatarDisplay) {
                        avatarDisplay.src = e.target.result;
                        avatarDisplay.style.display = 'block';
                    }
                    if (avatarLetter) {
                        avatarLetter.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    }

    function showToast(message = 'Profile updated successfully!') {
        const toast = document.getElementById('toast');
        const toastText = toast.querySelector('span');
        toastText.textContent = message;
        toast.classList.add('show');
        setTimeout(() => {
            toast.classList.remove('show');
        }, 3000);
    }

    // Form submission with enhanced feedback
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);
        
        // Add loading state
        const saveBtn = floatingSave?.querySelector('button');
        const originalBtnText = saveBtn?.innerHTML;
        if (saveBtn) {
            saveBtn.innerHTML = '<svg class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>Saving...';
            saveBtn.disabled = true;
        }
        
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showToast('Profile updated successfully! ✨');
                
                // Update display values
                const displayName = document.getElementById('displayName');
                const displayBio = document.getElementById('displayBio');
                
                if (displayName) {
                    displayName.textContent = formData.get('name') || '{{ $user->username }}';
                }
                if (displayBio) {
                    displayBio.textContent = formData.get('bio') || 'Life is what happens when you\'re busy making other plans.';
                }
                
                // Exit editing mode
                isEditing = false;
                container.classList.remove('editing');
                if (editBtn) editBtn.style.display = 'inline-flex';
                if (cancelBtn) cancelBtn.style.display = 'none';
                if (floatingSave) floatingSave.classList.remove('show');
                
                // Disable inputs
                const nameInput = form.querySelector('input[name="name"]');
                const bioInput = form.querySelector('textarea[name="bio"]');
                if (nameInput) nameInput.disabled = true;
                if (bioInput) bioInput.disabled = true;
                
            } else {
                showToast('Failed to update profile. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred. Please try again.');
        })
        .finally(() => {
            // Restore button state
            if (saveBtn && originalBtnText) {
                saveBtn.innerHTML = originalBtnText;
                saveBtn.disabled = false;
            }
        });
    });

    // Initialize form inputs as disabled if not editing
    const nameInput = form.querySelector('input[name="name"]');
    const bioInput = form.querySelector('textarea[name="bio"]');
    if (nameInput) nameInput.disabled = true;
    if (bioInput) bioInput.disabled = true;

    // Smooth scroll for moments
    const momentCards = document.querySelectorAll('.moment-card');
    momentCards.forEach(card => {
        card.addEventListener('click', function() {
            // Add click animation
            this.style.transform = 'scale(0.95)';
            setTimeout(() => {
                this.style.transform = '';
            }, 150);
        });
    });

    // Add intersection observer for animations
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, observerOptions);

    // Observe stat items for staggered animation
    const statItems = document.querySelectorAll('.stat-item');
    statItems.forEach((item, index) => {
        item.style.opacity = '0';
        item.style.transform = 'translateY(20px)';
        item.style.transition = `opacity 0.6s ease ${index * 0.1}s, transform 0.6s ease ${index * 0.1}s`;
        observer.observe(item);
    });

    // Observe moment cards
    momentCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = `opacity 0.6s ease ${index * 0.05}s, transform 0.6s ease ${index * 0.05}s`;
        observer.observe(card);
    });
});
</script>
@endpush