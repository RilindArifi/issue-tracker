<?php

namespace App\Http\Controllers;

use App\Http\Requests\AttachTagRequest;
use App\Http\Requests\StoreTagRequest;
use App\Models\Issue;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TagController extends Controller
{
    /**
     * List all tags. Returns JSON for AJAX, a view otherwise.
     */
    public function index(Request $request): JsonResponse|View
    {
        $tags = Tag::orderBy('name')->get();

        if ($request->wantsJson()) {
            return response()->json(['data' => $tags]);
        }

        return view('tags.index', compact('tags'));
    }

    /**
     * Create a tag with a unique name. Returns the created tag as JSON.
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = Tag::create($request->validated());

        return response()->json(['data' => $tag], 201);
    }

    /**
     * Attach a tag to an issue (AJAX). Idempotent via syncWithoutDetaching.
     */
    public function attach(AttachTagRequest $request, Issue $issue): JsonResponse
    {
        $issue->tags()->syncWithoutDetaching([$request->validated('tag_id')]);

        return response()->json([
            'data' => $issue->tags()->orderBy('name')->get(),
        ]);
    }

    /**
     * Detach a tag from an issue (AJAX).
     */
    public function detach(Issue $issue, Tag $tag): JsonResponse
    {
        $issue->tags()->detach($tag->id);

        return response()->json([
            'data' => $issue->tags()->orderBy('name')->get(),
        ]);
    }
}
