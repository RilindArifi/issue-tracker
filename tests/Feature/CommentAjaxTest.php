<?php

namespace Tests\Feature;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentAjaxTest extends TestCase
{
    use RefreshDatabase;

    public function test_a_comment_can_be_created_via_ajax(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();

        $response = $this->actingAs($user)->postJson(route('issues.comments.store', $issue), [
            'author_name' => 'Alice',
            'body' => 'Looks good!',
        ]);

        $response->assertCreated()
            ->assertJsonStructure(['html', 'total'])
            ->assertJsonFragment(['total' => 1]);

        $this->assertDatabaseHas('comments', [
            'issue_id' => $issue->id,
            'author_name' => 'Alice',
            'body' => 'Looks good!',
        ]);
    }

    public function test_comment_requires_author_and_body(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();

        $this->actingAs($user)
            ->postJson(route('issues.comments.store', $issue), ['author_name' => '', 'body' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['author_name', 'body']);
    }

    public function test_comments_are_paginated(): void
    {
        $user = User::factory()->create();
        $issue = Issue::factory()->for(Project::factory()->for($user, 'owner'))->create();
        Comment::factory()->count(7)->for($issue)->create();

        $page1 = $this->actingAs($user)->getJson(route('issues.comments.index', $issue));
        $page1->assertOk()
            ->assertJsonCount(5, 'html')
            ->assertJsonPath('meta.has_more', true)
            ->assertJsonPath('meta.total', 7);

        $this->actingAs($user)
            ->getJson(route('issues.comments.index', ['issue' => $issue, 'page' => 2]))
            ->assertOk()
            ->assertJsonCount(2, 'html')
            ->assertJsonPath('meta.has_more', false);
    }
}
