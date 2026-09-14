<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected static ?string $password = null;

    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'nim_nip' => 'USR-' . fake()->unique()->numerify('######'),
        ];
    }

    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * 'role' TIDAK ADA di $fillable model User (lihat App\Models\User),
     * jadi tidak bisa di-set lewat definition() di atas (mass assignment
     * akan mengabaikannya). Di-set eksplisit lewat afterCreating() supaya
     * konsisten dengan keputusan desain itu.
     */
    public function admin(): static
    {
        return $this->state(fn () => [
            'nim_nip' => 'ADMIN-' . fake()->unique()->numerify('###'),
        ])->afterCreating(function (User $user) {
            $user->role = 'admin';
            $user->save();
        });
    }

    public function dosen(): static
    {
        return $this->state(fn () => [
            'nim_nip' => 'NIP-' . fake()->unique()->numerify('######'),
        ])->afterCreating(function (User $user) {
            $user->role = 'dosen';
            $user->save();
        });
    }

    public function mahasiswa(): static
    {
        return $this->state(fn () => [
            'nim_nip' => (string) fake()->unique()->numberBetween(10230000, 10259999),
        ])->afterCreating(function (User $user) {
            $user->role = 'mahasiswa';
            $user->save();
        });
    }
}
