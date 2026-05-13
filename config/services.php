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

        'bot_token' => env('SLACK_BOT_TOKEN'),

        'channels' => [

            'orders' => env('SLACK_ORDERS_WEBHOOK'),

            'alerts' => env('SLACK_ALERTS_WEBHOOK'),

            'errors' => env('SLACK_ERRORS_WEBHOOK'),
        ],

        'signing_secret' => env('SLACK_SIGNING_SECRET'),
    ],

    'fake_store' => [
        'base_url' => env('FAKE_STORE_BASE_URL'),
        'token' => env('FAKE_STORE_TOKEN'),
    ],

    'notifications' => [
        'webhook' => [
            'url' => env('NOTIFICATION_WEBHOOK_URL'),
            'timeout' => (int) env('NOTIFICATION_WEBHOOK_TIMEOUT', 5),
            'retries' => (int) env('NOTIFICATION_WEBHOOK_RETRIES', 3),
            'retry_sleep_ms' => (int) env('NOTIFICATION_WEBHOOK_RETRY_SLEEP_MS', 200),
        ],
    ],

];
