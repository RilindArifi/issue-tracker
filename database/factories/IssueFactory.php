<?php

namespace Database\Factories;

use App\Enums\IssuePriority;
use App\Enums\IssueStatus;
use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Issue>
 */
class IssueFactory extends Factory
{
    public function definition(): array
    {
        return [
            'project_id' => Project::factory(),
            'title' => fake()->sentence(4),
            'description' => fake()->optional()->paragraph(),
            'status' => fake()->randomElement(IssueStatus::cases()),
            'priority' => fake()->randomElement(IssuePriority::cases()),
            'due_date' => fake()->optional()->dateTimeBetween('now', '+1 month'),
        ];
    }
}
