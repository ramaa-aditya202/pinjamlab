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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
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

    // Generic OAuth2 Configuration
    'oauth2' => [
        'client_id' => env('OAUTH2_CLIENT_ID'),
        'client_secret' => env('OAUTH2_CLIENT_SECRET'),
        'redirect' => env('OAUTH2_REDIRECT_URI'),
        'authorize_url' => env('OAUTH2_AUTHORIZE_URL', 'https://sso.maallathifahcikbar.sch.id/oauth/authorize'),
        'token_url' => env('OAUTH2_TOKEN_URL', 'https://sso.maallathifahcikbar.sch.id/oauth/token'),
        'user_url' => env('OAUTH2_USER_URL', 'https://sso.maallathifahcikbar.sch.id/api/user'),
    ],

    // N8N Configuration
    'n8n' => [
        'webhook_url' => env('N8N_WEBHOOK_URL'),
    ],

];
