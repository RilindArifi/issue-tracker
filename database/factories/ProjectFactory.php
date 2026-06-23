<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{
    public function definition(): array
    {
        $start = fake()->dateTimeBetween('-1 month', '+1 week');

        return [
            'user_id' => User::factory(),
            'name' => fake()->unique()->catchPhrase(),
            'description' => fake()->optional()->paragraph(),
            'start_date' => $start,
            'deadline' => fake()->dateTimeBetween($start, '+2 months'),
        ];
    }
}
