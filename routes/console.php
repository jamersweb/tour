<?php

use App\Services\Payments\NetworkNgeniusGateway;
use Database\Seeders\PackageSeeder;
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

Artisan::command('payments:probe-hosted-order', function (NetworkNgeniusGateway $gateway): int {
    $config = config('payments.network');

    if (! ($config['enabled'] ?? false)) {
        $this->error('NETWORK_PAYMENT_ENABLED is false. Enable it in .env first.');

        return 1;
    }

    $redirectUrl = route('payments.network.callback', ['transaction' => 1]);

    if (app()->environment('production') && ! str_starts_with($redirectUrl, 'https://')) {
        $this->warn('Callback URL is not HTTPS: '.$redirectUrl);
        $this->warn('Set APP_URL to your public https:// site URL — N-Genius often rejects http:// redirects in production.');
    }

    $this->line('Using redirectUrl: '.$redirectUrl);
    $this->line('Action: '.($config['action'] ?? 'PURCHASE').' | Outlet: '.($config['outlet_id'] ?? ''));

    try {
        $result = $gateway->probeHostedOrder($redirectUrl);
        $this->info('Hosted order OK — open this URL in a browser to confirm paypage loads:');
        $this->line($result['payment_url']);
        $this->line('Gateway order reference: '.$result['order_ref']);
        $this->warn('Creates a real order (minimum amount) in N-Genius; abandon payment if you only meant to test.');

        return 0;
    } catch (Throwable $e) {
        $this->error($e->getMessage());

        return 1;
    }
})->purpose('Create a minimal N-Genius hosted order and print the paypage URL (diagnostics)');

Artisan::command('packages:seed {--force : Required to run in production}', function (): int {
    $this->call('db:seed', [
        '--class' => PackageSeeder::class,
        '--force' => (bool) $this->option('force'),
    ]);

    return 0;
})->purpose('Upsert tour packages from database/data/current-packages.json (slug is the key)');
