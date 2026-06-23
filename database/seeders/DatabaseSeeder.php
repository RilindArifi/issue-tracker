<?php

namespace Database\Seeders;

use App\Models\Comment;
use App\Models\Issue;
use App\Models\Project;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Two known users for easy login during the demo.
        $owner = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        $teammate = User::factory()->create([
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
        ]);

        $users = collect([$owner, $teammate]);

        // Eight reusable tags.
        $tags = collect([
            ['name' => 'bug', 'color' => '#ef4444'],
            ['name' => 'feature', 'color' => '#3b82f6'],
            ['name' => 'docs', 'color' => '#8b5cf6'],
            ['name' => 'urgent', 'color' => '#f97316'],
            ['name' => 'backend', 'color' => '#10b981'],
            ['name' => 'frontend', 'color' => '#06b6d4'],
            ['name' => 'design', 'color' => '#ec4899'],
            ['name' => 'chore', 'color' => '#6b7280'],
        ])->map(fn (array $attrs) => Tag::factory()->create($attrs));

        // Three projects, each with issues fully wired up.
        Project::factory()
            ->count(3)
            ->for($owner, 'owner')
            ->create()
            ->each(function (Project $project) use ($tags, $users) {
                Issue::factory()
                    ->count(fake()->numberBetween(3, 6))
                    ->for($project)
                    ->create()
                    ->each(function (Issue $issue) use ($tags, $users) {
                        $issue->tags()->attach(
                            $tags->random(fake()->numberBetween(1, 3))->pluck('id')
                        );

                        $issue->members()->attach(
                            $users->random(fake()->numberBetween(1, 2))->pluck('id')
                        );

                        Comment::factory()
                            ->count(fake()->numberBetween(0, 4))
                            ->for($issue)
                            ->create();
                    });
            });
    }
}
