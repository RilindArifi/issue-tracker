<?php

namespace Tests\Feature;

use App\Enums\IssueStatus;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IssueSearchTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_endpoint_returns_matching_rows_as_json(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();
        Issue::factory()->for($project)->create(['title' => 'Database migration fails']);
        Issue::factory()->for($project)->create(['title' => 'Button colour wrong']);

        $response = $this->actingAs($user)
            ->getJson(route('issues.search', ['search' => 'migration']));

        $response->assertOk()
            ->assertJsonCount(1, 'html')
            ->assertJsonPath('meta.total', 1)
            ->assertSee('Database migration fails', false);
    }

    public function test_search_endpoint_respects_status_filter(): void
    {
        $user = User::factory()->create();
        $project = Project::factory()->for($user, 'owner')->create();
        Issue::factory()->for($project)->create(['status' => IssueStatus::Open]);
        Issue::factory()->for($project)->create(['status' => IssueStatus::Closed]);

        $this->actingAs($user)
            ->getJson(route('issues.search', ['status' => 'open']))
            ->assertOk()
            ->assertJsonPath('meta.total', 1);
    }
}
