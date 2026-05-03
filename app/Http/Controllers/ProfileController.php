<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Post;
use App\Services\GamificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function __construct(private GamificationService $gamification) {}

    public function show(User $user)
    {
        $posts = Post::where('user_id', $user->id)
            ->where('is_removed', false)
            ->where('is_anonymous', false)
            ->orderByDesc('created_at')
            ->paginate(12);

        $badges = $this->gamification->getBadgeDetails(
            $user->socialScore?->badges ?? []
        );

        $isFriend = false;
        $isBlocked = false;

        if (Auth::check() && Auth::id() !== $user->id) {
            $isFriend = \App\Models\Friendship::where(function ($q) use ($user) {
                $q->where('sender_id', Auth::id())->where('receiver_id', $user->id);
            })->orWhere(function ($q) use ($user) {
                $q->where('sender_id', $user->id)->where('receiver_id', Auth::id());
            })->where('status', 'accepted')->exists();

            $isBlocked = \App\Models\Block::where('blocker_id', Auth::id())
                ->where('blocked_id', $user->id)
                ->exists();
        }

        return view('profile.show', compact('user', 'posts', 'badges', 'isFriend', 'isBlocked'));
    }

    public function edit()
    {
        return view('profile.edit', ['user' => Auth::user()]);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'       => 'nullable|string|max:50',
            'bio'        => 'nullable|string|max:200',
            'vibe_tags'  => 'nullable|array|max:5',
            'vibe_tags.*'=> 'string|max:20',
            'avatar'     => 'nullable|file|mimes:jpg,jpeg,png,webp|max:4096',
            'cover_image'=> 'nullable|file|mimes:jpg,jpeg,png,webp|max:8192',
        ]);

        $data = $request->only(['name', 'bio', 'vibe_tags']);

        if ($request->hasFile('avatar')) {
            if ($user->avatar && !str_starts_with($user->avatar, 'http')) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = Storage::url($request->file('avatar')->store('avatars', 'public'));
        }

        if ($request->hasFile('cover_image')) {
            if ($user->cover_image) {
                Storage::disk('public')->delete($user->cover_image);
            }
            $data['cover_image'] = Storage::url($request->file('cover_image')->store('covers', 'public'));
        }

        $user->update($data);

        return response()->json(['success' => true]);
    }
}
