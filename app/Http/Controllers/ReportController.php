<?php

namespace App\Http\Controllers;

use App\Services\ModerationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReportController extends Controller
{
    public function __construct(private ModerationService $moderation) {}

    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|in:post,confession,comment',
            'reportable_id'   => 'required|integer',
            'reason'          => 'required|in:spam,harassment,nsfw,misinformation,other',
            'details'         => 'nullable|string|max:500',
        ]);

        $modelClass = match ($request->reportable_type) {
            'post'       => \App\Models\Post::class,
            'confession' => \App\Models\Confession::class,
            'comment'    => \App\Models\Comment::class,
        };

        $content = $modelClass::findOrFail($request->reportable_id);
        $report  = $this->moderation->report(Auth::user(), $content, $request->reason, $request->details);
        $this->moderation->checkAutoRemove($content);

        return response()->json(['success' => true]);
    }
}
