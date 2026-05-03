<?php

namespace App\Http\Controllers;

use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NotificationController extends Controller
{
    public function __construct(private NotificationService $notifications) {}

    public function index()
    {
        $user = Auth::user();

        $notifications = DB::table('notifications')
            ->where('notifiable_type', \App\Models\User::class)
            ->where('notifiable_id', $user->id)
            ->orderByDesc('created_at')
            ->paginate(30);

        $this->notifications->markAllRead($user);

        return view('notifications.index', compact('notifications'));
    }

    public function unreadCount()
    {
        return response()->json([
            'count' => $this->notifications->unreadCount(Auth::user()),
        ]);
    }
}
