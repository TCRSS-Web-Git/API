<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\Dev\DevSeeder;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(RolePermissionSeeder::class);

        $user = User::factory()->create([
            'first_name' => 'Super',
            'last_name' => 'Admin',
            'email' => 'superadmin@wawa-x.com',
        ]);
        $user->assignRole(Role::where('name', 'Super Admin')->first());

        if (config('app.env') === 'local') {
            $this->call(DevSeeder::class);
        }
    }
}
