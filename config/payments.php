<?php

return [
    'network' => [
        'enabled' => env('NETWORK_PAYMENT_ENABLED', false),
        'base_url' => env('NETWORK_PAYMENT_BASE_URL', 'https://api-gateway.sandbox.ngenius-payments.com'),
        'outlet_id' => env('NETWORK_PAYMENT_OUTLET_ID'),
        'api_key' => env('NETWORK_PAYMENT_API_KEY'),
        'api_secret' => env('NETWORK_PAYMENT_API_SECRET'),
        'currency' => env('NETWORK_PAYMENT_CURRENCY', 'AED'),
        'action' => env('NETWORK_PAYMENT_ACTION', 'PURCHASE'),
    ],
];
