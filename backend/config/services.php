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

    'resend'   => [
        'key' => env('RESEND_KEY'),
    ],

    'ses'      => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack'    => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel'              => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'facebook' => [
        'client_id'     => env('FACEBOOK_APP_ID'),
        'client_secret' => env('FACEBOOK_APP_SECRET'),
        'client_token'  => env('FACEBOOK_CLIENT_TOKEN'),
        'redirect'      => env('FACEBOOK_REDIRECT'),
        'config_id'     => env('FACEBOOK_CONFIG_ID'),
        'webhook_verify_token' => env('FACEBOOK_WEBHOOK_VERIFY_TOKEN', 'BizzTest5161'),
        'redirect_login'=> env('FACEBOOK_REDIRECT_URI_LOGIN', rtrim(env('APP_URL'), '/') . '/auth/facebook/login-callback'),
    ],

    'whatsapp' => [
        'app_id'               => env('WHATSAPP_APP_ID'),
        'app_secret'           => env('WHATSAPP_APP_SECRET'),
        'client_token'         => env('WHATSAPP_CLIENT_TOKEN'),
        'redirect'             => env('WHATSAPP_REDIRECT'),
        'config_id'            => env('WHATSAPP_CONFIG_ID'),
        'webhook_verify_token' => env('WHATSAPP_WEBHOOK_VERIFY_TOKEN'),
    ],
    'razorpay' => [
        'key' => env('RAZOR_PAY_API_KEY'),
        'secret' => env('RAZOR_PAY_SECRET_KEY'),
    ],

    'google' => [
        'client_id'     => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect'      => env('GOOGLE_REDIRECT_URI'),
        'redirect_login'=> env('GOOGLE_REDIRECT_URI_LOGIN', env('APP_URL') . '/auth/google/login-callback'),
        'gemini_key'    => env('GEMINI_API_KEY'),
    ],
];
