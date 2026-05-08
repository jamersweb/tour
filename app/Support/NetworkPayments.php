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

        if (empty($config['outlet_id']) || empty($config['api_key'])) {
            return false;
        }

        $apiKey = trim((string) $config['api_key']);
        $apiSecret = trim((string) ($config['api_secret'] ?? ''));

        if ($apiSecret !== '') {
            return true;
        }

        if (str_contains($apiKey, ':')) {
            return true;
        }

        $decoded = base64_decode($apiKey, true);

        return $decoded !== false && str_contains($decoded, ':');
    }
}
