<?php

namespace Database\Factories;

use App\Models\Invite;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Invite>
 */
class InviteFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        /* @var User $user */
        $user = User::factory()->create();

        return [
            'email' => $user->email,
            'token' => Str::random(40),
            'user_id' => $user->id,
        ];
    }
}
