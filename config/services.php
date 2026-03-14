<?php

return [
    'whatsapp' => [
        'enabled' => env('WHATSAPP_NOTIFICATIONS_ENABLED', false),
        'api_url' => env('WHATSAPP_API_URL'),
        'token' => env('WHATSAPP_API_TOKEN'),
        'from' => env('WHATSAPP_FROM'),
    ],
];
