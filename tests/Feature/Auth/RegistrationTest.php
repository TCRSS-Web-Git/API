<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_users_can_register(): void
    {
        $response = $this->post(route('register'), [
            'first_name' => 'Test',
            'last_name' => 'User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertNoContent();
    }

    public function test_new_users_can_not_register_with_duplicate_email(): void
    {
        $user = User::factory()->create(['email' => 'test@example.com']);
        $mockUser = User::factory()->make();
        $response = $this->post(route('register'), [
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => 'test@example.com',
            'password' => $mockUser->password,
            'password_confirmation' => $mockUser->password,
        ]);

        $response->assertStatus(302);
        $this->assertDatabaseCount('users', 1);

        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'password' => $user->password,
        ]);
        $this->assertDatabaseMissing('users', [
            'first_name' => $mockUser->first_name,
            'last_name' => $mockUser->last_name,
            'email' => $mockUser->email,
            'password' => $mockUser->password,
        ]);
    }
}
