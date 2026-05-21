<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard'     => 'web',
        'passwords' => 'pats_accesos',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    */

    'guards' => [
        'web' => [
            'driver'   => 'session',
            'provider' => 'pats_accesos',
        ],

        'api' => [
            'driver'   => 'token',
            'provider' => 'pats_accesos',
            'hash'     => false,
        ],

        'pasaporte' => [
            'driver'   => 'session',
            'provider' => 'pats_accesos',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    */

    'providers' => [
        'pats_users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\PatsUser::class,
        ],

        'pats_accesos' => [
            'driver' => 'eloquent',
            'model'  => App\Models\PatsAcceso::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    */

    'passwords' => [
        'pats_users' => [
            'provider' => 'pats_users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        'pats_accesos' => [
            'provider' => 'pats_accesos',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
