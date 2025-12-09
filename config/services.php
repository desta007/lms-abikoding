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

    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
    ],

    'zoom' => [
        /*
        |--------------------------------------------------------------------------
        | Zoom Server-to-Server OAuth App Configuration
        |--------------------------------------------------------------------------
        |
        | Untuk menggunakan Zoom Server-to-Server OAuth App, Anda perlu:
        | 1. Buat Server-to-Server OAuth App di Zoom Marketplace
        | 2. Dapatkan Client ID, Client Secret, dan Account ID
        | 3. Set environment variables berikut di .env:
        |
        | ZOOM_CLIENT_ID=your_client_id
        | ZOOM_CLIENT_SECRET=your_client_secret
        | ZOOM_ACCOUNT_ID=your_account_id
        |
        | Catatan: Account ID biasanya terlihat di halaman OAuth App di Zoom Marketplace
        |
        */
        'client_id' => env('ZOOM_CLIENT_ID'),
        'client_secret' => env('ZOOM_CLIENT_SECRET'),
        'account_id' => env('ZOOM_ACCOUNT_ID'),
        
        // Legacy support (will use client_id/client_secret if not set)
        'api_key' => env('ZOOM_API_KEY', env('ZOOM_CLIENT_ID')),
        'api_secret' => env('ZOOM_API_SECRET', env('ZOOM_CLIENT_SECRET')),
    ],

];
