<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\PaymentTransaction;
use App\Services\Messaging\WhatsappNotificationService;
use App\Services\Payments\NetworkNgeniusGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Mockery\MockInterface;
use Tests\TestCase;

class NetworkWebhookTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_webhook_updates_transaction_when_gateway_order_reference_matches(): void
    {
        Config::set('payments.network.webhook_secret', 'whsec_test');

        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $experience::class,
            'payable_id' => $experience->id,
            'reference' => 'internal-ref-webhook-1',
            'status' => 'pending',
            'customer_name' => 'Webhook User',
            'customer_email' => 'webhook@example.com',
            'amount' => $experience->price_from,
            'amount_minor' => 95000,
            'currency' => 'AED',
            'gateway_order_ref' => 'gateway-order-webhook-1',
        ]);

        $this->mock(NetworkNgeniusGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('fetchOrder')
                ->once()
                ->with('gateway-order-webhook-1')
                ->andReturn([
                    'reference' => 'gateway-order-webhook-1',
                    '_embedded' => [
                        'payment' => [
                            [
                                'state' => 'PURCHASED',
                                '_id' => 'urn:payment:webhook-xyz',
                            ],
                        ],
                    ],
                ]);
        });

        Mail::fake();

        $this->mock(WhatsappNotificationService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendBookingConfirmation')->zeroOrMoreTimes();
        });

        $response = $this->postJson('/payments/network/webhook', [
            'reference' => 'gateway-order-webhook-1',
        ], [
            'X-Network-Webhook-Secret' => 'whsec_test',
        ]);

        $response->assertOk()
            ->assertJsonPath('matched', true)
            ->assertJsonPath('synced', true);

        $transaction->refresh();

        $this->assertSame('paid', $transaction->status);
        $this->assertSame('webhook-xyz', $transaction->gateway_payment_ref);
    }

    public function test_webhook_returns_unmatched_for_unknown_reference(): void
    {
        Config::set('payments.network.webhook_secret', 'whsec_test');

        $response = $this->postJson('/payments/network/webhook', [
            'reference' => 'unknown-order-ref',
        ], [
            'X-Network-Webhook-Secret' => 'whsec_test',
        ]);

        $response->assertOk()
            ->assertJsonPath('matched', false);
    }

    public function test_webhook_rejects_wrong_secret_when_configured(): void
    {
        Config::set('payments.network.webhook_secret', 'whsec_test');

        $this->postJson('/payments/network/webhook', ['reference' => 'x'], [
            'X-Network-Webhook-Secret' => 'wrong',
        ])->assertUnauthorized();
    }
}
