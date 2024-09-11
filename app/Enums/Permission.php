<?php

namespace App\Enums;

enum Permission: string
{
    case USERS_VIEW = 'USERS:VIEW';
//    case USERS_VIEW_DELETED = 'USERS:VIEW_DELETED';
    case USERS_CREATE = 'USERS:CREATE';
    case USERS_UPDATE = 'USERS:UPDATE';
    case USERS_DELETE = 'USERS:DELETE';

    public static function defaultSuperAdminPermissions(): array
    {
        $allPermissions = collect(Permission::cases())->pluck('value')->toArray();
        $except = [];

        return array_diff($allPermissions, $except);
    }

    public static function defaultAdminPermissions(): array
    {
        $allPermissions = collect(Permission::cases())->pluck('value')->toArray();
        $except = [
            Permission::USERS_CREATE->value,
            Permission::USERS_UPDATE->value,
            Permission::USERS_DELETE->value,
        ];

        return array_diff($allPermissions, $except);
    }
}
