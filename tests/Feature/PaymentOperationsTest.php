<?php

namespace Tests\Feature;

use App\Models\Package;
use App\Models\PaymentTransaction;
use App\Models\User;
use App\Services\Messaging\WhatsappNotificationService;
use App\Services\Payments\PaymentOperationsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Mockery\MockInterface;
use Tests\TestCase;

class PaymentOperationsTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_admin_can_download_invoice_pdf(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $package = Package::query()->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $package::class,
            'payable_id' => $package->id,
            'reference' => 'invoice-admin-1',
            'status' => 'paid',
            'customer_name' => 'Admin Test',
            'customer_email' => 'admin-invoice@example.com',
            'amount' => 200,
            'amount_minor' => 20000,
            'currency' => 'AED',
            'user_id' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get("/admin/payment-transactions/{$transaction->id}/invoice");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_payment_can_be_reconciled_and_refunded(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);
        $package = Package::query()->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $package::class,
            'payable_id' => $package->id,
            'reference' => 'ops-1',
            'status' => 'pending',
            'customer_name' => 'Ops Test',
            'customer_email' => 'ops@example.com',
            'amount' => 200,
            'amount_minor' => 20000,
            'currency' => 'AED',
        ]);

        Mail::fake();

        $this->mock(WhatsappNotificationService::class, function (MockInterface $mock): void {
            $mock->shouldNotReceive('sendBookingConfirmation');
        });

        $service = app(PaymentOperationsService::class);
        $service->reconcile($transaction, $admin, 'paid', 'Matched against merchant statement.');

        $transaction->refresh();

        $this->assertSame('paid', $transaction->status);
        $this->assertNotNull($transaction->paid_at);
        $this->assertNotNull($transaction->reconciled_at);
        $this->assertSame($admin->id, $transaction->reconciled_by);
        $this->assertStringContainsString('Matched against merchant statement.', (string) $transaction->notes);

        $service->refund($transaction, $admin, 'Customer cancelled after manual review.');

        $transaction->refresh();

        $this->assertSame('refunded', $transaction->status);
        $this->assertNotNull($transaction->refunded_at);
        $this->assertSame($admin->id, $transaction->refunded_by);
        $this->assertStringContainsString('Customer cancelled after manual review.', (string) $transaction->notes);
    }

    public function test_resend_confirmation_forces_new_confirmation_timestamp(): void
    {
        $package = Package::query()->firstOrFail();

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $package::class,
            'payable_id' => $package->id,
            'reference' => 'ops-2',
            'status' => 'paid',
            'customer_name' => 'Retry Test',
            'customer_email' => 'retry@example.com',
            'customer_phone' => '+971500000777',
            'amount' => 200,
            'amount_minor' => 20000,
            'currency' => 'AED',
            'confirmation_sent_at' => now()->subDay(),
        ]);

        Mail::fake();

        $this->mock(WhatsappNotificationService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('sendBookingConfirmation')->once();
        });

        $service = app(PaymentOperationsService::class);
        $previous = $transaction->confirmation_sent_at;

        $service->resendConfirmation($transaction);

        $transaction->refresh();

        $this->assertTrue($transaction->confirmation_sent_at->gt($previous));
        Mail::assertSentCount(1);
    }
}
