<?php

namespace App\Services\Messaging;

use App\Models\PaymentTransaction;
use App\Models\PaymentTransactionTraveler;
use Illuminate\Http\Client\Factory as HttpFactory;
use RuntimeException;

class WhatsappNotificationService
{
    public function __construct(
        protected HttpFactory $http,
    ) {
    }

    public function sendBookingConfirmation(PaymentTransaction $transaction, string $name, string $phone): void
    {
        $config = $this->config();

        $response = $this->http->acceptJson()
            ->withToken($config['token'])
            ->post($config['api_url'], [
                'from' => $config['from'],
                'to' => $phone,
                'type' => 'text',
                'text' => [
                    'body' => $this->messageBody($transaction, $name),
                ],
            ]);

        if ($response->failed()) {
            throw new RuntimeException('WhatsApp notification request failed.');
        }
    }

    protected function messageBody(PaymentTransaction $transaction, string $name): string
    {
        $itemTitle = $transaction->bookingTitle();
        $travelDate = $transaction->travel_date?->format('F j, Y') ?? 'your selected date';

        if ($transaction->isCartCheckout()) {
            return "Hello {$name}, your cart booking for {$itemTitle} has been confirmed. Reference: {$transaction->reference}. Acute Tourism will contact you shortly with the next steps.";
        }

        return "Hello {$name}, your booking for {$itemTitle} has been confirmed for {$travelDate}. Reference: {$transaction->reference}. Acute Tourism will contact you shortly with the next steps.";
    }

    protected function config(): array
    {
        $config = config('services.whatsapp');

        if (! ($config['enabled'] ?? false)) {
            throw new RuntimeException('WhatsApp notifications are not enabled.');
        }

        foreach (['api_url', 'token', 'from'] as $key) {
            if (empty($config[$key])) {
                throw new RuntimeException("Missing WhatsApp configuration: {$key}");
            }
        }

        return $config;
    }
}
