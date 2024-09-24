<?php

namespace Tests;

use App\Models\Role;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Laravel\Sanctum\Sanctum;

abstract class TestCase extends BaseTestCase
{
    protected function signInAdmin($user = null): User
    {
        $this->seed(RolePermissionSeeder::class);

        $user = $user ?? User::factory()->create(['first_name' => 'Admin', 'last_name' => 'Test', 'email' => 'admin@test.com']);
        $role = Role::where('name', 'admin')->first();
        $user->assignRole($role);

        return $this->signIn($user);
    }

    protected function signIn($user = null): User
    {
        $user = $user ?? User::factory()->create();

        $this->actingAs($user);

        return $user;
    }

    public function sanctumSignIn($user = null, $abilities = ['*']): User
    {
        $user = $user ?? User::factory()->create();

        Sanctum::actingAs(User::factory()->create(), $abilities);

        return $user;
    }
}
