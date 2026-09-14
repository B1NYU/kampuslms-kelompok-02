<?php

namespace Database\Factories;

use App\Models\Submission;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class GradeFactory extends Factory
{
    public function definition(): array
    {
        return [
            'submission_id' => Submission::factory(),
            'graded_by' => User::factory()->dosen(),
            'score' => fake()->randomFloat(2, 55, 100),
            'feedback' => fake()->optional(0.6)->sentence(),
            'graded_at' => now(),
        ];
    }
}
