<?php

namespace Database\Seeders\Dev;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // create 10 users with random role from currently available roles
        $roles = Role::all();

        User::factory(20)
            ->create()
            ->each(function ($user) use ($roles) {
                $user->assignRole($roles->random());
            });
    }
}
