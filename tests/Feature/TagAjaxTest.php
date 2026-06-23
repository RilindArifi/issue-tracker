<?php

namespace Tests\Feature;

use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_tag_can_be_attached_to_an_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();
        $tag = Tag::factory()->create(['name' => 'urgent']);

        $response = $this->actingAs($user)
            ->postJson(route('issues.tags.attach', $issue), ['tag_id' => $tag->id]);

        $response->assertOk()->assertJsonFragment(['name' => 'urgent']);
        $this->assertTrue($issue->tags()->where('tags.id', $tag->id)->exists());
    }

    public function test_attaching_the_same_tag_twice_is_idempotent(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();
        $tag = Tag::factory()->create();

        $this->actingAs($user)->postJson(route('issues.tags.attach', $issue), ['tag_id' => $tag->id]);
        $this->actingAs($user)->postJson(route('issues.tags.attach', $issue), ['tag_id' => $tag->id]);

        $this->assertSame(1, $issue->tags()->count());
    }

    public function test_a_tag_can_be_detached_from_an_issue(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();
        $tag = Tag::factory()->create();
        $issue->tags()->attach($tag);

        $this->actingAs($user)
            ->deleteJson(route('issues.tags.detach', [$issue, $tag]))
            ->assertOk();

        $this->assertFalse($issue->tags()->where('tags.id', $tag->id)->exists());
    }

    public function test_attaching_an_invalid_tag_returns_422(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();

        $this->actingAs($user)
            ->postJson(route('issues.tags.attach', $issue), ['tag_id' => 99999])
            ->assertStatus(422)
            ->assertJsonValidationErrors('tag_id');
    }
}
