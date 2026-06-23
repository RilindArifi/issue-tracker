<?php

namespace App\Policies;

use App\Models\Project;
use App\Models\User;

class ProjectPolicy
{
    /**
     * Any authenticated user can view the project list.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Any authenticated user can view a single project.
     */
    public function view(User $user, Project $project): bool
    {
        return true;
    }

    /**
     * Any authenticated user can create a project.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Only the owner can update the project.
     */
    public function update(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }

    /**
     * Only the owner can delete the project.
     */
    public function delete(User $user, Project $project): bool
    {
        return $user->id === $project->user_id;
    }
}
