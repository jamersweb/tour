<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Support\Arr;
use RuntimeException;

class NetworkNgeniusGateway
{
    public function __construct(
        protected HttpFactory $http,
    ) {}

    /**
     * Create a hosted payment session (N-Genius Online order).
     *
     * @see https://www.network.ae/en/merchant-solutions/ecommerce-payments/n-genius-online
     * @see https://www.network.ae/en/merchant-solutions/ecommerce-payments/developers
     */
    public function createHostedOrder(
        PaymentTransaction $transaction,
        string $redirectUrl,
        ?string $cancelRedirectUrl = null,
    ): array {
        $config = $this->config();
        $accessToken = $this->accessToken();

        $merchantAttributes = [
            'redirectUrl' => $redirectUrl,
        ];

        if (filled($cancelRedirectUrl)) {
            $merchantAttributes['cancelRedirectUrl'] = $cancelRedirectUrl;
        }

        $response = $this->http->baseUrl($config['base_url'])
            ->withToken($accessToken)
            ->acceptJson()
            ->contentType('application/vnd.ni-payment.v2+json')
            ->withHeaders([
                'Accept' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post("/transactions/outlets/{$config['outlet_id']}/orders", [
                'action' => $config['action'],
                'amount' => [
                    'currencyCode' => $transaction->currency,
                    'value' => $transaction->amount_minor,
                ],
                'language' => 'en',
                'emailAddress' => $transaction->customer_email,
                'merchantAttributes' => $merchantAttributes,
                'reference' => $transaction->reference,
            ])
            ->throw()
            ->json();

        $paymentUrl = Arr::get($response, '_links.payment.href');
        $orderRef = Arr::get($response, 'reference');

        if (! $paymentUrl || ! $orderRef) {
            throw new RuntimeException('Network payment gateway did not return a hosted payment URL.');
        }

        return [
            'payment_url' => $paymentUrl,
            'order_ref' => $orderRef,
            'payload' => $response,
        ];
    }

    public function fetchOrder(string $orderRef): array
    {
        $config = $this->config();
        $accessToken = $this->accessToken();

        return $this->http->baseUrl($config['base_url'])
            ->withToken($accessToken)
            ->acceptJson()
            ->withHeaders([
                'Accept' => 'application/vnd.ni-payment.v2+json',
            ])
            ->get("/transactions/outlets/{$config['outlet_id']}/orders/{$orderRef}")
            ->throw()
            ->json();
    }

    /**
     * Health check: obtain an access token from the identity service (validates API key + secret + base URL).
     *
     * @throws RuntimeException
     */
    public function verifyCredentials(): string
    {
        return $this->accessToken();
    }

    protected function accessToken(): string
    {
        $config = $this->config();
        $basic = base64_encode($config['api_key'].':'.$config['api_secret']);
        $realm = $this->identityRealm($config);

        $body = json_encode([
            'grant_type' => 'client_credentials',
            'realm' => $realm,
        ], JSON_THROW_ON_ERROR);

        $response = $this->http->baseUrl($config['base_url'])
            ->withHeaders([
                'Authorization' => 'Basic '.$basic,
                'Accept' => 'application/vnd.ni-identity.v1+json',
                'Content-Type' => 'application/vnd.ni-identity.v1+json',
            ])
            ->withBody($body, 'application/vnd.ni-identity.v1+json')
            ->post('/identity/auth/access-token')
            ->throw()
            ->json();

        $token = Arr::get($response, 'access_token');

        if (! $token) {
            throw new RuntimeException('Network payment gateway access token was not returned.');
        }

        return $token;
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function identityRealm(array $config): string
    {
        $explicit = $config['realm'] ?? null;
        if (filled($explicit)) {
            return (string) $explicit;
        }

        $base = (string) ($config['base_url'] ?? '');

        return str_contains($base, 'sandbox') ? 'ni' : 'networkinternational';
    }

    protected function config(): array
    {
        $config = config('payments.network');

        if (! ($config['enabled'] ?? false)) {
            throw new RuntimeException('Network payment gateway is not enabled.');
        }

        foreach (['base_url', 'outlet_id', 'api_key', 'api_secret'] as $key) {
            if (empty($config[$key])) {
                throw new RuntimeException("Missing Network payment configuration: {$key}");
            }
        }

        return $config;
    }
}
