<?php

use App\Services\Payments\NetworkNgeniusGateway;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('payments:verify-network', function (NetworkNgeniusGateway $gateway): int {
    $config = config('payments.network');

    if (! ($config['enabled'] ?? false)) {
        $this->error('NETWORK_PAYMENT_ENABLED (or NGENIUS_ENABLED) is false. Set to true in .env to verify.');

        return 1;
    }

    try {
        $token = $gateway->verifyCredentials();
        $this->info('N-Genius identity OK: received access token ('.strlen($token).' chars).');
        $this->line('Base URL: '.($config['base_url'] ?? ''));
        $this->line('Outlet: '.($config['outlet_id'] ?? ''));

        return 0;
    } catch (Throwable $e) {
        $this->error($e->getMessage());

        return 1;
    }
})->purpose('Verify N-Genius API credentials (access token from identity service)');
