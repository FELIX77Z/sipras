<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'sipras' => [
        'super_admin_email' => env('SIPRAS_SUPER_ADMIN_EMAIL', 'ikhwan@gmail.com'),
        'super_admin_password' => env('SIPRAS_SUPER_ADMIN_PASSWORD'),
        'technician_email' => env('SIPRAS_TECHNICIAN_EMAIL', 'teknisi@sipras.test'),
        'technician_password' => env('SIPRAS_TECHNICIAN_PASSWORD', 'password'),
    ],

];
