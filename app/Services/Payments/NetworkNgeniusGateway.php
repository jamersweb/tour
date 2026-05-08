<?php

namespace App\Services\Payments;

use App\Models\PaymentTransaction;
use Illuminate\Http\Client\Factory as HttpFactory;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
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

        if ($config['skip_confirmation_page'] ?? false) {
            $merchantAttributes['skipConfirmationPage'] = true;
        }

        if (filled($cancelRedirectUrl)) {
            // Align with Postman HPP examples.
            $merchantAttributes['cancelUrl'] = $cancelRedirectUrl;
        }

        $paymentAttempts = $config['payment_attempts'] ?? null;
        if ($paymentAttempts !== null && $paymentAttempts !== '' && (int) $paymentAttempts > 0) {
            // Postman sample sends this value under merchantAttributes.
            $merchantAttributes['paymentAttempts'] = (string) ((int) $paymentAttempts);
        }

        $showPayerName = $config['show_payer_name'] ?? null;
        if ($showPayerName !== null && $showPayerName !== '') {
            $merchantAttributes['showPayerName'] = filter_var($showPayerName, FILTER_VALIDATE_BOOLEAN);
        }

        $currency = strtoupper((string) ($transaction->currency ?: ($config['currency'] ?? 'AED')));

        $orderPayload = [
            'action' => $config['action'],
            'emailAddress' => $transaction->customer_email,
            'amount' => [
                'currencyCode' => $currency,
                'value' => $transaction->amount_minor,
            ],
            'merchantOrderReference' => $transaction->reference,
            'merchantAttributes' => $merchantAttributes,
        ];

        $httpResponse = $this->http->baseUrl($config['base_url'])
            ->timeout(60)
            ->withToken($accessToken)
            ->acceptJson()
            ->contentType('application/vnd.ni-payment.v2+json')
            ->withHeaders([
                'Accept' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post("/transactions/outlets/{$config['outlet_id']}/orders", $orderPayload);

        $response = $this->decodeOrderResponseOrThrow($httpResponse);

        // Paypage href is usually _links.payment; some responses use cnp:payment-link (see N-Genius docs).
        $paymentUrl = Arr::get($response, '_links.payment.href')
            ?? Arr::get($response, '_links.cnp:payment-link.href');
        $orderRef = Arr::get($response, 'reference');

        if (! $paymentUrl || ! $orderRef) {
            throw new RuntimeException(
                'Network payment gateway did not return a hosted payment URL. Response: '.$this->truncateBody(json_encode($response) ?: ''),
            );
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
        $basic = $this->basicAuthValue($config);

        $bodies = $this->identityRequestBodies($config);
        $lastBody = '';
        $lastResponse = null;

        foreach ($bodies as $attempt) {
            $httpResponse = $this->postIdentityAccessToken($config, $basic, $attempt['body']);
            $lastBody = $attempt['label'];
            $lastResponse = $httpResponse;

            if ($httpResponse->successful()) {
                $response = $httpResponse->json();
                $token = Arr::get($response, 'access_token');
                if ($token) {
                    return $token;
                }

                throw new RuntimeException('Network payment gateway access token was not returned.');
            }

            $bodyText = $this->truncateBody($httpResponse->body());

            if (! $this->identityFailureShouldRetry($httpResponse->status(), $bodyText)) {
                throw new RuntimeException(sprintf(
                    'N-Genius identity failed (%s) [body: %s]: %s',
                    $httpResponse->status(),
                    $lastBody,
                    $bodyText,
                ));
            }
        }

        throw new RuntimeException(sprintf(
            'N-Genius identity failed after %d attempts (last body %s). Last response (%s): %s. '
            .'Check NETWORK_PAYMENT_API_KEY is the raw base64 from the portal (no "Basic " prefix), '
            .'NETWORK_PAYMENT_BASE_URL matches the key environment (sandbox vs live), and outlet id.',
            count($bodies),
            $lastBody,
            $lastResponse?->status() ?? '?',
            $this->truncateBody($lastResponse?->body() ?? ''),
        ));
    }

    /**
     * @return list<array{body:?string,label:string}> JSON string body or null for raw empty body.
     */
    protected function identityRequestBodies(array $config): array
    {
        $explicit = $config['identity_send_realm_name'] ?? null;
        $resolvedRealm = $this->identityRealm($config);

        if ($explicit !== null && $explicit !== '') {
            if (filter_var($explicit, FILTER_VALIDATE_BOOLEAN)) {
                return [[
                    'body' => json_encode(['realmName' => $resolvedRealm], JSON_THROW_ON_ERROR),
                    'label' => '{"realmName":"'.$resolvedRealm.'"}',
                ]];
            }

            return [
                ['body' => '{}', 'label' => '{}'],
                ['body' => null, 'label' => '(empty)'],
            ];
        }

        $isSandbox = str_contains((string) ($config['base_url'] ?? ''), 'sandbox');
        $realmLive = $resolvedRealm;

        if ($isSandbox) {
            return [
                ['body' => json_encode(['realmName' => 'ni'], JSON_THROW_ON_ERROR), 'label' => '{"realmName":"ni"}'],
                ['body' => json_encode(['realmName' => $realmLive], JSON_THROW_ON_ERROR), 'label' => '{"realmName":"'.$realmLive.'"}'],
                ['body' => '{}', 'label' => '{}'],
                ['body' => null, 'label' => '(empty)'],
            ];
        }

        // Live: never send realm "ni" - that is sandbox/UAT only.
        return [
            // Many working integrations send an empty JSON object for live token requests.
            ['body' => '{}', 'label' => '{}'],
            ['body' => null, 'label' => '(empty)'],
            ['body' => json_encode(['realmName' => $realmLive], JSON_THROW_ON_ERROR), 'label' => '{"realmName":"'.$realmLive.'"}'],
            ['body' => json_encode(['realmName' => 'NetworkInternational'], JSON_THROW_ON_ERROR), 'label' => '{"realmName":"NetworkInternational"}'],
        ];
    }

    protected function identityFailureShouldRetry(int $status, string $bodyText): bool
    {
        if ($status === 400 && str_contains($bodyText, 'badTokenRequest')) {
            return true;
        }

        if ($status === 404 && str_contains($bodyText, 'realmNameNotAvailable')) {
            return true;
        }

        if ($status === 404 && str_contains($bodyText, 'Unable to find a tenant')) {
            return true;
        }

        return false;
    }

    protected function postIdentityAccessToken(array $config, string $basic, ?string $jsonBody): Response
    {
        $headers = [
            'Authorization' => 'Basic '.$basic,
            'Accept' => 'application/vnd.ni-identity.v1+json',
            'Content-Type' => 'application/vnd.ni-identity.v1+json',
        ];

        $request = $this->http->baseUrl($config['base_url'])
            ->timeout(30)
            ->withHeaders($headers);

        if ($jsonBody !== null) {
            $request = $request->withBody($jsonBody, 'application/vnd.ni-identity.v1+json');
        } else {
            $request = $request->withBody('', 'application/vnd.ni-identity.v1+json');
        }

        return $request->post('/identity/auth/access-token');
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function basicAuthValue(array $config): string
    {
        $apiKey = trim((string) ($config['api_key'] ?? ''));
        // Copy/paste from Postman/cURL sometimes includes the "Basic " prefix — strip it.
        $apiKey = (string) preg_replace('#^Basic\s+#i', '', $apiKey);
        $apiSecret = trim((string) ($config['api_secret'] ?? ''));

        if ($apiSecret !== '') {
            return base64_encode($apiKey.':'.$apiSecret);
        }

        // Support a single pre-encoded value: base64("api_key:api_secret")
        // or a raw "api_key:api_secret" string in NETWORK_PAYMENT_API_KEY.
        if (str_contains($apiKey, ':')) {
            return base64_encode($apiKey);
        }

        $decoded = base64_decode($apiKey, true);
        if ($decoded !== false && str_contains($decoded, ':')) {
            return $apiKey;
        }

        throw new RuntimeException('Invalid Network payment credentials: provide api_key+api_secret, or set NETWORK_PAYMENT_API_KEY to base64("api_key:api_secret").');
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

        foreach (['base_url', 'outlet_id', 'api_key'] as $key) {
            if (empty($config[$key])) {
                throw new RuntimeException("Missing Network payment configuration: {$key}");
            }
        }

        return $config;
    }

    /**
     * Minimal hosted order to verify outlet + paypage from the server (creates a real order in N-Genius).
     *
     * @return array{payment_url: string, order_ref: string, payload: array}
     */
    public function probeHostedOrder(string $redirectUrl): array
    {
        $config = $this->config();
        $accessToken = $this->accessToken();
        $currency = strtoupper((string) ($config['currency'] ?? 'AED'));

        $merchantAttributes = [
            'redirectUrl' => $redirectUrl,
        ];
        if ($config['skip_confirmation_page'] ?? false) {
            $merchantAttributes['skipConfirmationPage'] = true;
        }

        $paymentAttempts = $config['payment_attempts'] ?? null;
        if ($paymentAttempts !== null && $paymentAttempts !== '' && (int) $paymentAttempts > 0) {
            $merchantAttributes['paymentAttempts'] = (string) ((int) $paymentAttempts);
        }

        $showPayerName = $config['show_payer_name'] ?? null;
        if ($showPayerName !== null && $showPayerName !== '') {
            $merchantAttributes['showPayerName'] = filter_var($showPayerName, FILTER_VALIDATE_BOOLEAN);
        }

        $probePayload = [
            'action' => $config['action'],
            'emailAddress' => 'checkout-probe@example.invalid',
            'amount' => [
                'currencyCode' => $currency,
                'value' => 100,
            ],
            'merchantOrderReference' => 'probe-'.Str::upper(Str::random(12)),
            'merchantAttributes' => $merchantAttributes,
        ];

        $httpResponse = $this->http->baseUrl($config['base_url'])
            ->timeout(60)
            ->withToken($accessToken)
            ->acceptJson()
            ->contentType('application/vnd.ni-payment.v2+json')
            ->withHeaders([
                'Accept' => 'application/vnd.ni-payment.v2+json',
            ])
            ->post("/transactions/outlets/{$config['outlet_id']}/orders", $probePayload);

        $response = $this->decodeOrderResponseOrThrow($httpResponse);

        $paymentUrl = Arr::get($response, '_links.payment.href')
            ?? Arr::get($response, '_links.cnp:payment-link.href');
        $orderRef = Arr::get($response, 'reference');

        if (! $paymentUrl || ! $orderRef) {
            throw new RuntimeException(
                'Probe: no paypage URL in response: '.$this->truncateBody(json_encode($response) ?: ''),
            );
        }

        return [
            'payment_url' => $paymentUrl,
            'order_ref' => $orderRef,
            'payload' => $response,
        ];
    }

    /**
     * @return array<string, mixed>
     */
    protected function decodeOrderResponseOrThrow(Response $httpResponse): array
    {
        if ($httpResponse->failed()) {
            throw new RuntimeException(sprintf(
                'N-Genius create order failed (%s): %s',
                $httpResponse->status(),
                $this->truncateBody($httpResponse->body()),
            ));
        }

        $response = $httpResponse->json();

        if (! is_array($response)) {
            throw new RuntimeException('N-Genius returned a non-JSON order response: '.$this->truncateBody($httpResponse->body()));
        }

        return $response;
    }

    protected function truncateBody(string $body, int $max = 8000): string
    {
        if (strlen($body) <= $max) {
            return $body;
        }

        return substr($body, 0, $max).'…';
    }
}

