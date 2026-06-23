<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCommentRequest;
use App\Models\Comment;
use App\Models\Issue;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

class CommentController extends Controller
{
    /**
     * Paginated comments for an issue (AJAX). Each comment is rendered
     * server-side with the shared partial so the markup lives in one place.
     */
    public function index(Issue $issue): JsonResponse
    {
        $comments = $issue->comments()->paginate(5);

        return response()->json([
            'html' => $comments->map(fn (Comment $comment) => view('partials.comment-item', [
                'comment' => $comment,
            ])->render())->all(),
            'meta' => [
                'current_page' => $comments->currentPage(),
                'last_page' => $comments->lastPage(),
                'has_more' => $comments->hasMorePages(),
                'total' => $comments->total(),
            ],
        ]);
    }

    /**
     * Store a new comment (AJAX) and return its rendered markup so the
     * frontend can prepend it without rebuilding the template in JS.
     */
    public function store(StoreCommentRequest $request, Issue $issue): JsonResponse
    {
        $comment = $issue->comments()->create($request->validated());

        return response()->json([
            'html' => view('partials.comment-item', ['comment' => $comment])->render(),
            'total' => $issue->comments()->count(),
        ], 201);
    }
}
