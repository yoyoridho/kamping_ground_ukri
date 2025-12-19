<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | This option defines the default authentication "guard" and password
    | reset "broker" for your application. You may change these values
    | as required, but they're a perfect start for most applications.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'users'),
    ],

   'guards' => [
    'web' => ['driver' => 'session', 'provider' => 'users'],

    'pengunjung' => ['driver' => 'session', 'provider' => 'pengunjung'],
  'pegawai' => ['driver' => 'session', 'provider' => 'pegawai'],
],



'providers' => [
  'users' => ['driver' => 'eloquent', 'model' => App\Models\User::class],

  'pengunjung' => ['driver' => 'eloquent', 'model' => App\Models\Pengunjung::class],
  'pegawai' => ['driver' => 'eloquent', 'model' => App\Models\Pegawai::class],
],


    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],



    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Here you may define the number of seconds before a password confirmation
    | window expires and users are asked to re-enter their password via the
    | confirmation screen. By default, the timeout lasts for three hours.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
