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
    ) {
    }

    public function createHostedOrder(PaymentTransaction $transaction, string $redirectUrl): array
    {
        $config = $this->config();
        $accessToken = $this->accessToken();

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
                'merchantAttributes' => [
                    'redirectUrl' => $redirectUrl,
                ],
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

    protected function accessToken(): string
    {
        $config = $this->config();
        $basic = base64_encode($config['api_key'].':'.$config['api_secret']);

        $response = $this->http->baseUrl($config['base_url'])
            ->withHeaders([
                'Authorization' => 'Basic '.$basic,
                'Accept' => 'application/vnd.ni-identity.v1+json',
            ])
            ->post('/identity/auth/access-token')
            ->throw()
            ->json();

        $token = Arr::get($response, 'access_token');

        if (! $token) {
            throw new RuntimeException('Network payment gateway access token was not returned.');
        }

        return $token;
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
