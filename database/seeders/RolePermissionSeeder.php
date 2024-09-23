<?php

namespace Database\Seeders;

use App\Enums\Permission as PermissionEnum;
use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::firstOrCreate([
            'name' => 'Super Admin',
            'guard_name' => 'web',
        ]);
        $adminRole = Role::firstOrCreate([
            'name' => 'Admin',
            'guard_name' => 'web',
        ]);

        // Users Management
        Permission::firstOrCreate(['name' => PermissionEnum::USERS_VIEW, 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => PermissionEnum::USERS_CREATE, 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => PermissionEnum::USERS_UPDATE, 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => PermissionEnum::USERS_DELETE, 'guard_name' => 'web']);

        // Blogs Management
        Permission::firstOrCreate(['name' => PermissionEnum::BLOGS_VIEW, 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => PermissionEnum::BLOGS_CREATE, 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => PermissionEnum::BLOGS_UPDATE, 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => PermissionEnum::BLOGS_DELETE, 'guard_name' => 'web']);

        $superAdminRole->syncPermissions(PermissionEnum::defaultSuperAdminPermissions());
        $adminRole->syncPermissions(PermissionEnum::defaultAdminPermissions());
    }
}
