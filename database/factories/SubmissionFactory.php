<?php

namespace Database\Factories;

use App\Models\Assignment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class SubmissionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'assignment_id' => Assignment::factory(),
            'user_id' => User::factory()->mahasiswa(),
            'file_path' => 'submissions/' . fake()->uuid() . '.pdf',
            'original_name' => fake()->slug(3) . '.pdf',
            'file_size' => fake()->numberBetween(50_000, 5_000_000),
            'note' => fake()->optional(0.4)->sentence(),
            'submitted_at' => now(),
            'is_late' => false,
        ];
    }

    public function late(): static
    {
        return $this->state(fn () => ['is_late' => true]);
    }
}
