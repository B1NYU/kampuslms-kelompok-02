<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class AssignmentFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'created_by' => User::factory()->dosen(),
            'title' => 'Tugas ' . ucfirst(fake()->words(3, true)),
            'instructions' => fake()->paragraphs(2, true),
            'due_at' => fake()->dateTimeBetween('-1 week', '+2 weeks'),
            'max_score' => 100,
            'allow_late' => true,
            'status' => 'published',
        ];
    }

    public function pastDeadline(): static
    {
        return $this->state(fn () => [
            'due_at' => fake()->dateTimeBetween('-3 weeks', '-1 week'),
            'status' => 'published',
        ]);
    }

    public function active(): static
    {
        return $this->state(fn () => [
            'due_at' => fake()->dateTimeBetween('+3 days', '+3 weeks'),
            'status' => 'published',
        ]);
    }

    public function draft(): static
    {
        return $this->state(fn () => [
            'due_at' => fake()->dateTimeBetween('+1 week', '+5 weeks'),
            'status' => 'draft',
        ]);
    }
}
