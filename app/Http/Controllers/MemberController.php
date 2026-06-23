<?php

namespace App\Http\Controllers;

use App\Http\Requests\AssignMemberRequest;
use App\Models\Issue;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class MemberController extends Controller
{
    /**
     * Assign a user to an issue (AJAX). Idempotent via syncWithoutDetaching.
     */
    public function attach(AssignMemberRequest $request, Issue $issue): JsonResponse
    {
        $issue->members()->syncWithoutDetaching([$request->validated('user_id')]);

        return response()->json([
            'data' => $issue->members()->orderBy('name')->get(['users.id', 'name']),
        ]);
    }

    /**
     * Remove a user from an issue (AJAX).
     */
    public function detach(Issue $issue, User $user): JsonResponse
    {
        $issue->members()->detach($user->id);

        return response()->json([
            'data' => $issue->members()->orderBy('name')->get(['users.id', 'name']),
        ]);
    }
}
