<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectPolicyTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_owner_cannot_delete_a_project(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();

        $this->actingAs($other)
            ->delete(route('projects.destroy', $project))
            ->assertForbidden();

        $this->assertModelExists($project);
    }

    public function test_non_owner_cannot_update_a_project(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create(['name' => 'Original']);

        $this->actingAs($other)
            ->put(route('projects.update', $project), ['name' => 'Hacked'])
            ->assertForbidden();

        $this->assertSame('Original', $project->fresh()->name);
    }

    public function test_non_owner_cannot_view_edit_form(): void
    {
        $owner = User::factory()->create();
        $other = User::factory()->create();
        $project = Project::factory()->for($owner, 'owner')->create();

        $this->actingAs($other)
            ->get(route('projects.edit', $project))
            ->assertForbidden();
    }
}
