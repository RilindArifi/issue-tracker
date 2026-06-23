<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MemberAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_member_can_be_assigned_to_an_issue(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create(['name' => 'Jane']);
        $issue = Issue::factory()->for(Project::factory()->for($owner, 'owner'))->create();

        $this->actingAs($owner)
            ->postJson(route('issues.members.attach', $issue), ['user_id' => $member->id])
            ->assertOk()
            ->assertJsonFragment(['name' => 'Jane']);

        $this->assertTrue($issue->members()->where('users.id', $member->id)->exists());
    }

    public function test_a_member_can_be_removed_from_an_issue(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($owner, 'owner'))->create();
        $issue->members()->attach($member);

        $this->actingAs($owner)
            ->deleteJson(route('issues.members.detach', [$issue, $member]))
            ->assertOk();

        $this->assertFalse($issue->members()->where('users.id', $member->id)->exists());
    }

    public function test_assigning_an_invalid_user_returns_422(): void
    {
        $owner = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($owner, 'owner'))->create();

        $this->actingAs($owner)
            ->postJson(route('issues.members.attach', $issue), ['user_id' => 99999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('user_id');
    }
}
