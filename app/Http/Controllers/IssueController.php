<?php

namespace App\Http\Controllers;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Http\Requests\StoreIssueRequest;
use App\Http\Requests\UpdateIssueRequest;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IssueController extends Controller
{
    /**
     * List issues with optional status/priority/tag/search filters.
     * Uses query scopes from the Issue model and eager loads to avoid N+1.
     */
    public function index(Request $request): View
    {
        $issues = Issue::query()
            ->with(['project', 'tags'])
            ->status($request->query('status'))
            ->priority($request->query('priority'))
            ->tag($request->query('tag'))
            ->search($request->query('search'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        $tags = Tag::orderBy('name')->get();

        return view('issues.index', [
            'issues' => $issues,
            'tags' => $tags,
            'statuses' => IssueStatus::options(),
            'priorities' => IssuePriority::options(),
            'filters' => $request->only(['status', 'priority', 'tag', 'search']),
        ]);
    }

    /**
     * AJAX search/filter for the issues index. Returns rows rendered with the
     * shared partial plus pagination metadata, so the live list reuses the same
     * markup as the server-rendered page.
     */
    public function search(Request $request): JsonResponse
    {
        $issues = Issue::query()
            ->with(['project', 'tags'])
            ->status($request->query('status'))
            ->priority($request->query('priority'))
            ->tag($request->query('tag'))
            ->search($request->query('search'))
            ->latest()
            ->paginate(12)
            ->withQueryString();

        return response()->json([
            'html' => $issues->map(fn (Issue $issue) => view('partials.issue-row', [
                'issue' => $issue,
            ])->render())->all(),
            'meta' => [
                'total' => $issues->total(),
                'current_page' => $issues->currentPage(),
                'last_page' => $issues->lastPage(),
            ],
        ]);
    }

    public function create(Request $request): View
    {
        return view('issues.create', [
            'projects' => Project::orderBy('name')->get(),
            'selectedProject' => $request->query('project_id'),
            'statuses' => IssueStatus::options(),
            'priorities' => IssuePriority::options(),
        ]);
    }

    public function store(StoreIssueRequest $request): RedirectResponse
    {
        $issue = Issue::create($request->validated());

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue created.');
    }

    /**
     * Show an issue with its project, tags, members and comments.
     */
    public function show(Issue $issue): View
    {
        $issue->load(['project', 'tags', 'members', 'comments']);

        return view('issues.show', [
            'issue' => $issue,
            'allTags' => Tag::orderBy('name')->get(),
            'allUsers' => \App\Models\User::orderBy('name')->get(),
        ]);
    }

    public function edit(Issue $issue): View
    {
        return view('issues.edit', [
            'issue' => $issue,
            'projects' => Project::orderBy('name')->get(),
            'statuses' => IssueStatus::options(),
            'priorities' => IssuePriority::options(),
        ]);
    }

    public function update(UpdateIssueRequest $request, Issue $issue): RedirectResponse
    {
        $issue->update($request->validated());

        return redirect()
            ->route('issues.show', $issue)
            ->with('status', 'Issue updated.');
    }

    public function destroy(Issue $issue): RedirectResponse
    {
        $project = $issue->project;
        $issue->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Issue deleted.');
    }
}
