<?php

use App\Models\Permission;
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

        Permission::class => [
            'salt' => env('HASHIDS_PERMISSION', 'hQvnpYx9FRgXQJqjb7Vd'),
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
