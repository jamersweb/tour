<?php

namespace Tests\Feature;

use App\Mail\BookingConfirmedMail;
use App\Mail\CheckoutContinuePaymentMail;
use App\Mail\StaffBookingPaidMail;
use App\Mail\StaffNewPaymentTransactionMail;
use App\Models\Experience;
use App\Models\PaymentTransaction;
use App\Services\Messaging\WhatsappNotificationService;
use App\Services\Payments\NetworkNgeniusGateway;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Inertia\Testing\AssertableInertia as Assert;
use Mockery\MockInterface;
use Tests\TestCase;

class CheckoutTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected function setUp(): void
    {
        parent::setUp();
        $this->enableNetworkCheckoutForTests();
    }

    public function test_checkout_redirects_when_gateway_not_fully_configured(): void
    {
        config([
            'payments.network.enabled' => false,
        ]);

        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $this->get("/checkout/experiences/{$experience->slug}")
            ->assertRedirect(route('experiences.show', $experience->slug));
    }

    public function test_experience_checkout_page_renders_for_priced_experience(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $response = $this->get("/checkout/experiences/{$experience->slug}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Checkout/Show')
            ->where('checkout.slug', $experience->slug)
            ->where('checkout.type', 'experience')
        );
    }

    public function test_checkout_start_creates_transaction_and_redirects_to_gateway(): void
    {
        Mail::fake();

        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $this->mock(NetworkNgeniusGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('createHostedOrder')
                ->once()
                ->andReturn([
                    'payment_url' => 'https://pay.example.test/session/123',
                    'order_ref' => 'order-ref-123',
                    'payload' => ['reference' => 'order-ref-123'],
                ]);
        });

        $response = $this->post("/checkout/experiences/{$experience->slug}", [
            'name' => 'Bilal Ahmed',
            'email' => 'bilal@example.com',
            'phone' => '+971500000001',
            'travel_date' => now()->addWeek()->toDateString(),
            'guest_count' => 2,
            'traveler_contacts' => [
                [
                    'name' => 'Bilal Ahmed',
                    'email' => 'bilal@example.com',
                    'phone' => '+971500000001',
                ],
                [
                    'name' => 'Hassan Ali',
                    'email' => 'hassan@example.com',
                    'phone' => '+971500000002',
                ],
            ],
        ]);

        $response->assertRedirect('https://pay.example.test/session/123');

        $this->assertDatabaseHas((new PaymentTransaction)->getTable(), [
            'customer_name' => 'Bilal Ahmed',
            'customer_email' => 'bilal@example.com',
            'gateway_order_ref' => 'order-ref-123',
            'status' => 'pending',
        ]);

        $this->assertDatabaseHas('payment_transaction_travelers', [
            'name' => 'Hassan Ali',
            'email' => 'hassan@example.com',
        ]);

        Mail::assertSent(CheckoutContinuePaymentMail::class, 1);
        Mail::assertSent(StaffNewPaymentTransactionMail::class, 1);
    }

    public function test_checkout_callback_updates_transaction_status(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $experience::class,
            'payable_id' => $experience->id,
            'gateway' => 'network-ngenius',
            'reference' => 'internal-ref-1',
            'status' => 'pending',
            'customer_name' => 'Sara Khan',
            'customer_email' => 'sara@example.com',
            'amount' => $experience->price_from,
            'amount_minor' => 95000,
            'currency' => 'AED',
            'gateway_order_ref' => 'gateway-order-1',
        ]);

        $transaction->travelers()->createMany([
            [
                'position' => 1,
                'name' => 'Sara Khan',
                'email' => 'sara@example.com',
                'phone' => '+971500000010',
            ],
            [
                'position' => 2,
                'name' => 'Ayesha Khan',
                'email' => 'ayesha@example.com',
                'phone' => '+971500000011',
            ],
        ]);

        $this->mock(NetworkNgeniusGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('fetchOrder')
                ->once()
                ->andReturn([
                    'reference' => 'gateway-order-1',
                    '_embedded' => [
                        'payment' => [
                            [
                                'state' => 'PURCHASED',
                                '_id' => 'urn:payment:abc123',
                            ],
                        ],
                    ],
                ]);
        });

        Mail::fake();

        $this->mock(WhatsappNotificationService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendBookingConfirmation')->times(2);
        });

        $response = $this->get("/payments/network/callback?transaction={$transaction->id}");

        $response->assertRedirect("/checkout/result/{$transaction->id}");

        $transaction->refresh();

        $this->assertSame('paid', $transaction->status);
        $this->assertSame('abc123', $transaction->gateway_payment_ref);
        $this->assertNotNull($transaction->paid_at);
        $this->assertNotNull($transaction->confirmation_sent_at);
        Mail::assertSent(BookingConfirmedMail::class, 3);
        Mail::assertSent(StaffBookingPaidMail::class, 1);
        $this->assertDatabaseHas('payment_transaction_travelers', [
            'payment_transaction_id' => $transaction->id,
            'email' => 'ayesha@example.com',
        ]);
    }

    public function test_checkout_start_requires_traveler_contacts_to_match_guest_count(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $response = $this->from("/checkout/experiences/{$experience->slug}")
            ->post("/checkout/experiences/{$experience->slug}", [
                'name' => 'Bilal Ahmed',
                'email' => 'bilal@example.com',
                'phone' => '+971500000001',
                'travel_date' => now()->addWeek()->toDateString(),
                'guest_count' => 2,
                'traveler_contacts' => [
                    [
                        'name' => 'Bilal Ahmed',
                        'email' => 'bilal@example.com',
                        'phone' => '+971500000001',
                    ],
                ],
            ]);

        $response->assertRedirect("/checkout/experiences/{$experience->slug}");
        $response->assertSessionHasErrors('traveler_contacts');
    }

    public function test_confirmation_is_not_resent_on_repeat_paid_callback(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $experience::class,
            'payable_id' => $experience->id,
            'gateway' => 'network-ngenius',
            'reference' => 'internal-ref-2',
            'status' => 'paid',
            'customer_name' => 'Sara Khan',
            'customer_email' => 'sara@example.com',
            'customer_phone' => '+971500000010',
            'amount' => $experience->price_from,
            'amount_minor' => 95000,
            'currency' => 'AED',
            'gateway_order_ref' => 'gateway-order-2',
            'confirmation_sent_at' => now()->subMinute(),
        ]);

        Mail::fake();

        $this->mock(NetworkNgeniusGateway::class, function (MockInterface $mock): void {
            $mock->shouldReceive('fetchOrder')
                ->once()
                ->andReturn([
                    'reference' => 'gateway-order-2',
                    '_embedded' => [
                        'payment' => [
                            [
                                'state' => 'PURCHASED',
                                '_id' => 'urn:payment:def456',
                            ],
                        ],
                    ],
                ]);
        });

        $this->mock(WhatsappNotificationService::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('sendBookingConfirmation');
        });

        $this->get("/payments/network/callback?transaction={$transaction->id}")
            ->assertRedirect("/checkout/result/{$transaction->id}");

        Mail::assertNothingSent();
    }
}
