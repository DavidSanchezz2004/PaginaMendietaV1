<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
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
    'aqpf' => [
    'base_url' => env('AQPF_BASE_URL', 'https://apis.aqpfact.pe/api'),
    'token' => env('AQPF_API_TOKEN'),
    ],
    'feasy' => [
    'base_url' => env('FEASY_BASE_URL', 'https://api.feasyperu.com/api'),
    'token' => env('FEASY_TOKEN'),
    'auth_mode' => env('FEASY_AUTH_MODE', 'bearer'),
    'timeout' => (int) env('FEASY_TIMEOUT', 30),
    ],
    'robot' => [
        'base_url' => env('ROBOT_BASE_URL','http://127.0.0.1:8000'),
        'api_key' => env('ROBOT_API_KEY'),
        'cf_client_id' => env('CF_ACCESS_CLIENT_ID'),
        'cf_client_secret' => env('CF_ACCESS_CLIENT_SECRET'),
        'timeout' => env('ROBOT_TIMEOUT', 60),
    ],
    'n8n' => [
        'assistant_url' => env('N8N_ASSISTANT_URL'),
        'timeout' => (int) env('N8N_ASSISTANT_TIMEOUT', 25),
        'token' => env('N8N_ASSISTANT_TOKEN'),
    ],

];
