<?php

namespace App\Support;

class NetworkPayments
{
    /**
     * True when hosted checkout can run (matches gateway preflight in NetworkNgeniusGateway).
     */
    public static function isCheckoutReady(): bool
    {
        $config = config('payments.network', []);

        if (! ($config['enabled'] ?? false)) {
            return false;
        }

        foreach (['outlet_id', 'api_key', 'api_secret'] as $key) {
            if (empty($config[$key])) {
                return false;
            }
        }

        return true;
    }
}
