<?php

use App\Models\Award;
use App\Models\Blog;
use App\Models\Career;
use App\Models\Category;
use App\Models\Media;
use App\Models\Permission;
use App\Models\ProductAndService;
use App\Models\Role;
use App\Models\User;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the connections below you wish to use as
    | your default connection for all work. Of course, you may use many
    | connections at once using the manager class.
    |
    */

    'default' => 'main',

    /*
    |--------------------------------------------------------------------------
    | Hashids Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the connections setup for your application. Example
    | configuration has been included, but you may add as many connections as
    | you would like.
    |
    */

    'connections' => [
        'main' => [
            'salt' => '',
            'length' => 0,
            // 'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],

        'alternative' => [
            'salt' => 'your-salt-string',
            'length' => 'your-length-integer',
            // 'alphabet' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
        ],

        // Please keep it in alphabetical order.

        Award::class => [
            'salt' => env('HASHIDS_AWARD', 'PF8pczSGd46fBCFNnnmL'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        Blog::class => [
            'salt' => env('HASHIDS_BLOG', 'FR4PmB7XHx4VXBrC8hHh'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        Category::class => [
            'salt' => env('HASHIDS_CATEGORY', '7eDm9NeRRUAruPmEFnPd'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        Career::class => [
            'salt' => env('HASHIDS_CAREER', '02daee7be1849d32b55c'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        Media::class => [
            'salt' => env('HASHIDS_MEDIA', 'uwyS5b76zmHr2cJDbwmn'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        Permission::class => [
            'salt' => env('HASHIDS_PERMISSION', 'hQvnpYx9FRgXQJqjb7Vd'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        ProductAndService::class => [
            'salt' => env('HASHIDS_PRODUCT_AND_SERVICE', '8YuZxjeTRL1VXPiticpv'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        Role::class => [
            'salt' => env('HASHIDS_ROLE', 'MEb4EWRN6KAL7QfAQMJw'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

        User::class => [
            'salt' => env('HASHIDS_USER', 'fdMpJD8QbuAUx9HvJzse'),
            'length' => 14,
            'alphabet' => 'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789',
        ],

    ],

];
