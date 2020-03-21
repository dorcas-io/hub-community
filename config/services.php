<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Stripe, Mailgun, SparkPost and others. This file provides a sane
    | default location for this type of information, allowing packages
    | to have a conventional place to find your various credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
    ],

    'ses' => [
        'key' => env('AWS_KEY_EKO'),
        'secret' => env('AWS_SECRET_EKO'),
        'region' => env('AWS_SES_REGION_EKO', 'us-east-1'),
    ],

    'sparkpost' => [
        'secret' => env('SPARKPOST_SECRET'),
    ],

    'paystack' => [
        'public_key' => env('PAYSTACK_PUBLIC_KEY'),
        'secret_key' => env('PAYSTACK_SECRET_KEY'),
    ],

    'reseller_club' => [
        'id' => env('RESELLER_CLUB_ID', '732644'),
        'api_key' => env('RESELLER_CLUB_API_KEY', 'l7PUTAvSMoMdGgHFsEjJtMyEkqcxs2yt'),
        'env' => env('RESELLER_CLUB_ENV', 'test')
    ],
    
    'upperlink' => [
        'secret' => 'sRHszgQU115VvSLKE9f3bPlB3iH02Wl!suxqR?A-a4PTWYtCfdVRjwFOTVChNHOXzBLFSnNDZim-f0G8bxcvoWo8-k!m9fXAhLW0Kb6U0I7HfaqLAKfTo0QKl8pMl6gvatBysZ8QgplcXUoKm3H=FZ0WHnGc-xrYvvbqaSROkcQPsnU8drdNcu5NreFh5NUwV0HleqEl',
        'identifier' => env('UPPERLINK_ID', 'GURj00QMZbGn')
    ]
];
