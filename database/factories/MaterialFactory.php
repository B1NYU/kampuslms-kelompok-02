<?php

namespace Database\Factories;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class MaterialFactory extends Factory
{
    public function definition(): array
    {
        $name = fake()->slug(3) . '.pdf';

        return [
            'course_id' => Course::factory(),
            'session' => fake()->numberBetween(1, 7),
            'uploaded_by' => User::factory()->dosen(),
            'title' => ucfirst(fake()->words(3, true)),
            'description' => fake()->optional(0.5)->sentence(),
            'type' => 'file',
            'file_path' => 'materials/' . fake()->uuid() . '.pdf',
            'original_name' => $name,
            'file_size' => fake()->numberBetween(20_000, 2_000_000),
            'mime_type' => 'application/pdf',
            'external_url' => null,
        ];
    }

    public function link(string $url = 'https://laravel.com/docs'): static
    {
        return $this->state(fn () => [
            'type' => 'link',
            'file_path' => null,
            'original_name' => null,
            'file_size' => null,
            'mime_type' => null,
            'external_url' => $url,
        ]);
    }
}
