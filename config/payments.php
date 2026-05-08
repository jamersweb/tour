<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Network International — N-Genius Online (hosted payment page)
    |--------------------------------------------------------------------------
    |
    | Obtain sandbox or live API credentials and outlet ID from Network.
    | Set APP_URL to a publicly reachable HTTPS URL so redirectUrl / callback work
    | (use ngrok or similar for local testing).
    |
    | Docs: https://www.network.ae/en/merchant-solutions/ecommerce-payments/developers
    |
    | Typical base URLs:
    | - Sandbox: https://api-gateway.sandbox.ngenius-payments.com
    | - Production: https://api-gateway.ngenius-payments.com
    |
    */
    'network' => [
        'enabled' => env('NETWORK_PAYMENT_ENABLED', env('NGENIUS_ENABLED', false)),
        // NETWORK_PAYMENT_* is the primary naming; NGENIUS_* is supported as an alias (same values).
        'base_url' => env('NETWORK_PAYMENT_BASE_URL', env('NGENIUS_API_URL', 'https://api-gateway.sandbox.ngenius-payments.com')),
        'outlet_id' => env('NETWORK_PAYMENT_OUTLET_ID', env('NGENIUS_OUTLET_REF')),
        'api_key' => env('NETWORK_PAYMENT_API_KEY', env('NGENIUS_API_KEY')),
        'api_secret' => env('NETWORK_PAYMENT_API_SECRET', env('NGENIUS_API_SECRET')),
        'currency' => env('NETWORK_PAYMENT_CURRENCY', 'AED'),
        // PURCHASE = sale. Use AUTH for authorize-only flows (capture later in portal).
        'action' => env('NETWORK_PAYMENT_ACTION', 'PURCHASE'),
        // Identity: production often accepts an empty POST body with Basic auth (see Network curl samples). Sandbox may need realmName — set true to force {"realmName":...}.
        'identity_send_realm_name' => env('NETWORK_PAYMENT_IDENTITY_SEND_REALM_NAME'),
        // When identity_send_realm_name is true/unset for sandbox, realmName value (default ni / networkinternational from base_url).
        'realm' => env('NETWORK_PAYMENT_REALM', env('NGENIUS_REALM')),
        // Hosted order merchantAttributes (matches common working Postman payloads).
        'skip_confirmation_page' => env('NETWORK_PAYMENT_SKIP_CONFIRMATION_PAGE', true),
        'payment_attempts' => env('NETWORK_PAYMENT_PAYMENT_ATTEMPTS', 3),
        'show_payer_name' => env('NETWORK_PAYMENT_SHOW_PAYER_NAME'),

        /*
        | Outbound webhook (Network → your app). Set a long random value and send it as header
        | X-Network-Webhook-Secret on each request. Required in production; optional in local/testing.
        | Register URL: POST {APP_URL}/payments/network/webhook
        */
        'webhook_secret' => env('NETWORK_PAYMENT_WEBHOOK_SECRET'),
    ],
];
