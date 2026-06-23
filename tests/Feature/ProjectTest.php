<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProjectTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $this->get('/projects')->assertRedirect('/login');
    }

    public function test_index_lists_projects(): void
    {
        $user = User::factory()->create();
        Project::factory()->for($user, 'owner')->create(['name' => 'Apollo']);

        $this->actingAs($user)
            ->get('/projects')
            ->assertOk()
            ->assertSee('Apollo');
    }

    public function test_a_project_can_be_created(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/projects', [
            'name' => 'New Project',
            'description' => 'Some description',
            'start_date' => '2026-01-01',
            'deadline' => '2026-02-01',
        ]);

        $project = Project::firstWhere('name', 'New Project');

        $this->assertNotNull($project);
        $this->assertSame($user->id, $project->user_id);
        $response->assertRedirect(route('projects.show', $project));
    }

    public function test_project_name_is_required(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/projects', ['name' => ''])
            ->assertSessionHasErrors('name');
    }

    public function test_deadline_must_be_after_or_equal_to_start_date(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->post('/projects', [
                'name' => 'Bad dates',
                'start_date' => '2026-02-01',
                'deadline' => '2026-01-01',
            ])
            ->assertSessionHasErrors('deadline');
    }

    public function test_owner_can_update_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        $this->actingAs($user)
            ->put(route('projects.update', $project), ['name' => 'Renamed'])
            ->assertRedirect(route('projects.show', $project));

        $this->assertSame('Renamed', $project->fresh()->name);
    }

    public function test_owner_can_delete_their_project(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        $this->actingAs($user)
            ->delete(route('projects.destroy', $project))
            ->assertRedirect(route('projects.index'));

        $this->assertModelMissing($project);
    }
}
