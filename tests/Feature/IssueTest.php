<?php

namespace Tests\Feature;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueTest extends TestCase
{
    use RefreshDatabase;

    public function test_an_issue_can_be_created(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        $response = $this->actingAs($user)->post('/issues', [
            'project_id' => $project->id,
            'title' => 'Login is broken',
            'status' => IssueStatus::Open->value,
            'priority' => IssuePriority::High->value,
        ]);

        $issue = Issue::firstWhere('title', 'Login is broken');

        $this->assertNotNull($issue);
        $this->assertSame(IssueStatus::Open, $issue->status);
        $this->assertSame(IssuePriority::High, $issue->priority);
        $response->assertRedirect(route('issues.show', $issue));
    }

    public function test_issue_title_is_required(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        $this->actingAs($user)
            ->post('/issues', [
                'project_id' => $project->id,
                'title' => '',
                'status' => IssueStatus::Open->value,
                'priority' => IssuePriority::Low->value,
            ])
            ->assertSessionHasErrors('title');
    }

    public function test_invalid_status_is_rejected(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        $this->actingAs($user)
            ->post('/issues', [
                'project_id' => $project->id,
                'title' => 'Bad status',
                'status' => 'not_a_status',
                'priority' => IssuePriority::Low->value,
            ])
            ->assertSessionHasErrors('status');
    }

    public function test_issues_can_be_filtered_by_status(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();

        Issue::factory()->for($project)->create(['title' => 'Open one', 'status' => IssueStatus::Open]);
        Issue::factory()->for($project)->create(['title' => 'Closed one', 'status' => IssueStatus::Closed]);

        $this->actingAs($user)
            ->get('/issues?status=open')
            ->assertOk()
            ->assertSee('Open one')
            ->assertDontSee('Closed one');
    }

    public function test_issues_can_be_filtered_by_tag(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();
        $tag = Tag::factory()->create();

        $tagged = Issue::factory()->for($project)->create(['title' => 'Tagged issue']);
        $tagged->tags()->attach($tag);
        Issue::factory()->for($project)->create(['title' => 'Untagged issue']);

        $this->actingAs($user)
            ->get("/issues?tag={$tag->id}")
            ->assertOk()
            ->assertSee('Tagged issue')
            ->assertDontSee('Untagged issue');
    }

    public function test_an_issue_can_be_updated(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();
        $issue = Issue::factory()->for($project)->create(['title' => 'Old title']);

        $this->actingAs($user)->put(route('issues.update', $issue), [
            'project_id' => $project->id,
            'title' => 'New title',
            'status' => IssueStatus::InProgress->value,
            'priority' => IssuePriority::Medium->value,
        ])->assertRedirect(route('issues.show', $issue));

        $this->assertSame('New title', $issue->fresh()->title);
    }

    public function test_an_issue_can_be_deleted(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();
        $issue = Issue::factory()->for($project)->create();

        $this->actingAs($user)
            ->delete(route('issues.destroy', $issue))
            ->assertRedirect(route('projects.show', $project));

        $this->assertModelMissing($issue);
    }
}
