<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Campus;
use App\Models\UserStreak;
use App\Models\SocialScore;
use App\Models\UserPresence;
use App\Services\AnonymousIdentityService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function __construct(private AnonymousIdentityService $identity) {}

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['username' => $request->username, 'password' => $request->password], $request->boolean('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();
            $user->update(['last_active_at' => now()]);

            UserPresence::updateOrCreate(
                ['user_id' => $user->id],
                ['is_online' => true, 'status' => 'online', 'last_seen_at' => now()]
            );

            return redirect()->intended(route('feed.index'));
        }

        return back()->withErrors(['username' => 'Invalid credentials.'])->withInput();
    }

    public function showRegister()
    {
        $campuses = Campus::where('is_active', true)->orderBy('name')->get();
        return view('auth.register', compact('campuses'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'username'  => 'required|string|min:3|max:30|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
            'phone'     => 'required|digits_between:10,15|unique:users,phone',
            'campus_id' => 'nullable|exists:campuses,id',
            'password'  => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'username'              => $request->username,
            'phone'                 => $request->phone,
            'campus_id'             => $request->campus_id,
            'password'              => Hash::make($request->password),
            'anonymous_alias'       => $this->identity->generateAlias(0, uniqid()),
            'anonymous_avatar_seed' => $this->identity->generateAvatarSeed(0, uniqid()),
        ]);

        // Bootstrap gamification records
        UserStreak::create(['user_id' => $user->id]);
        SocialScore::create(['user_id' => $user->id, 'badges' => ['early_adopter']]);
        UserPresence::create(['user_id' => $user->id, 'is_online' => true, 'status' => 'online']);

        // Update campus member count
        if ($user->campus_id) {
            Campus::where('id', $user->campus_id)->increment('member_count');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('feed.index')->with('welcome', true);
    }

    public function logout(Request $request)
    {
        $user = Auth::user();
        if ($user) {
            UserPresence::where('user_id', $user->id)
                ->update(['is_online' => false, 'status' => 'offline', 'last_seen_at' => now()]);
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home');
    }
}
