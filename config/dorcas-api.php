<?php
return [
    // the client environment
    'env' => env('DORCAS_ENV', 'local'),

    'url' => env('SDK_HOST_PRODUCTION', 'https://api.dorcas.ng'),

    /*
    |--------------------------------------------------------------------------
    | API Credentials
    |--------------------------------------------------------------------------
    |
    | You need to provide the credentials that will be used while communicating
    | with the Dorcas API.
    |
    |
    */
    'client' => [

        // the client ID provided to you for use with your app
        'id' => env('DORCAS_CLIENT_ID', 0),

        // the client secret
        'secret' => env('DORCAS_CLIENT_SECRET', '')
    ],
    
    'client_personal' => [
        // the client ID provided to you for use with your app
        'id' => env('DORCAS_PERSONAL_CLIENT_ID', 1),
    
        // the client secret
        'secret' => env('DORCAS_PERSONAL_CLIENT_SECRET', 'jA4GG1wJ8bFIKApWviFuyfNAN29737s258nVVCjk')
    ]
];