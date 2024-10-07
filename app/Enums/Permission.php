<?php

namespace App\Enums;

enum Permission: string
{
    case USERS_VIEW = 'USERS:VIEW';
    //    case USERS_VIEW_DELETED = 'USERS:VIEW_DELETED';
    case USERS_CREATE = 'USERS:CREATE';
    case USERS_UPDATE = 'USERS:UPDATE';
    case USERS_DELETE = 'USERS:DELETE';

    case BLOGS_VIEW = 'BLOGS:VIEW';
    case BLOGS_CREATE = 'BLOGS:CREATE';
    case BLOGS_UPDATE = 'BLOGS:UPDATE';
    case BLOGS_DELETE = 'BLOGS:DELETE';

    case BLOG_CATEGORIES_CREATE = 'BLOG_CATEGORIES:CREATE';
    case BLOG_CATEGORIES_UPDATE = 'BLOG_CATEGORIES:UPDATE';
    case BLOG_CATEGORIES_DELETE = 'BLOG_CATEGORIES:DELETE';

    case JOB_POST_VIEW = 'JOB_POST:VIEW';
    case JOB_POST_CREATE = 'JOB_POST:CREATE';
    case JOB_POST_UPDATE = 'JOB_POST:UPDATE';
    case JOB_POST_DELETE = 'JOB_POST:DELETE';

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
