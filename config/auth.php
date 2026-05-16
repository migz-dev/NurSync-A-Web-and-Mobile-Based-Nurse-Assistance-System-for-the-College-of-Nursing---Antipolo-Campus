<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],


    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    | Guards: web (students), faculty, admin
    */

    'guards' => [

        // Student / default user guard
        'web' => [
            'driver'   => 'session',
            'provider' => 'users',
        ],

        // Faculty guard
        'faculty' => [
            'driver'   => 'session',
            'provider' => 'faculty',
        ],

        // Admin guard
        'admin' => [
            'driver'   => 'session',
            'provider' => 'admins',
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    | Models: User, Faculty, Admin
    */

    'providers' => [

        // Students
        'users' => [
            'driver' => 'eloquent',
            'model'  => App\Models\User::class,
        ],

        // Faculty
        'faculty' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Faculty::class,
        ],

        // Admins
        'admins' => [
            'driver' => 'eloquent',
            'model'  => App\Models\Admin::class,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    | Separate brokers for Users, Faculty, Admin
    */

    'passwords' => [

        // Default: Students
        'users' => [
            'provider' => 'users',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        // Faculty
        'faculty' => [
            'provider' => 'faculty',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],

        // Admins
        'admins' => [
            'provider' => 'admins',
            'table'    => 'password_reset_tokens',
            'expire'   => 60,
            'throttle' => 60,
        ],
    ],


    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
