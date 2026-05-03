<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\ConfessionController;
use App\Http\Controllers\ReactionController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PollController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\LiveRoomController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Route;

// ── Public ────────────────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('feed.index'))->name('home');

// Auth
Route::middleware('guest')->group(function () {
    Route::get('/login',    [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',   [AuthController::class, 'login'])->name('login.post');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register',[AuthController::class, 'register'])->name('register.post');
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// ── Authenticated ─────────────────────────────────────────────────────────────
Route::middleware('auth')->group(function () {

    // Feed
    Route::get('/feed',          [FeedController::class, 'index'])->name('feed.index');
    Route::get('/feed/more',     [FeedController::class, 'loadMore'])->name('feed.more');

    // Posts
    Route::post('/posts',        [PostController::class, 'store'])->name('posts.store');
    Route::delete('/posts/{post}', [PostController::class, 'destroy'])->name('posts.destroy');

    // Confessions
    Route::get('/confessions',   [ConfessionController::class, 'index'])->name('confessions.index');
    Route::post('/confessions',  [ConfessionController::class, 'store'])->name('confessions.store');

    // Reactions (AJAX)
    Route::post('/reactions/toggle', [ReactionController::class, 'toggle'])->name('reactions.toggle');

    // Comments (AJAX)
    Route::post('/comments',     [CommentController::class, 'store'])->name('comments.store');

    // Polls (AJAX)
    Route::post('/polls/{poll}/vote', [PollController::class, 'vote'])->name('polls.vote');

    // Events
    Route::get('/events',        [EventController::class, 'index'])->name('events.index');
    Route::post('/events',       [EventController::class, 'store'])->name('events.store');
    Route::post('/events/{event}/attend', [EventController::class, 'attend'])->name('events.attend');

    // Live Rooms
    Route::get('/live/stats',    [LiveRoomController::class, 'stats'])->name('live.stats');
    Route::get('/live',          [LiveRoomController::class, 'index'])->name('live.index');
    Route::post('/live',         [LiveRoomController::class, 'store'])->name('live.store');
    Route::get('/live/{room}',   [LiveRoomController::class, 'show'])->name('live.show');
    Route::post('/live/{room}/message', [LiveRoomController::class, 'sendMessage'])->name('live.message');

    // Notifications
    Route::get('/notifications',       [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/count', [NotificationController::class, 'unreadCount'])->name('notifications.count');

    // Reports (AJAX)
    Route::post('/reports',      [ReportController::class, 'store'])->name('reports.store');

    // Profile
    Route::get('/profile/edit',  [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile',      [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/@{user:username}', [ProfileController::class, 'show'])->name('profile.show');
});
