<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartCheckoutRequest;
use App\Mail\CheckoutContinuePaymentMail;
use App\Models\Experience;
use App\Models\Package;
use App\Models\PaymentTransaction;
use App\Services\AdminBookingNotifier;
use App\Services\Payments\BookingConfirmationService;
use App\Services\Payments\NetworkNgeniusGateway;
use App\Services\Payments\NetworkPaymentSynchronizer;
use App\Services\Payments\PaymentTransactionLogger;
use App\Support\NetworkPayments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CheckoutController extends Controller
{
    public function experience(string $slug): InertiaResponse|RedirectResponse
    {
        $experience = Experience::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        abort_if(! $experience->price_from, 404);

        if (! NetworkPayments::isCheckoutReady()) {
            return redirect()
                ->route('experiences.show', $slug)
                ->with(
                    'error',
                    'Online payment is not available right now. Please plan this experience or contact us to complete your booking.',
                );
        }

        return Inertia::render('Checkout/Show', [
            'seo' => [
                'title' => 'Checkout',
                'description' => "Complete checkout for {$experience->title}.",
            ],
            'checkout' => $this->checkoutPayload(
                label: 'Experience Checkout',
                type: 'experience',
                slug: $experience->slug,
                title: $experience->title,
                summary: $experience->short_description,
                amount: $experience->price_from,
                currency: $experience->currency,
                image: $experience->hero_image_url,
            ),
        ]);
    }

    public function package(string $slug): InertiaResponse|RedirectResponse
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        abort_if(! $package->price_from, 404);

        if (! NetworkPayments::isCheckoutReady()) {
            return redirect()
                ->route('packages.show', $slug)
                ->with(
                    'error',
                    'Online payment is not available right now. Please contact us to book this package.',
                );
        }

        return Inertia::render('Checkout/Show', [
            'seo' => [
                'title' => 'Checkout',
                'description' => "Complete checkout for {$package->title}.",
            ],
            'checkout' => $this->checkoutPayload(
                label: 'Package Checkout',
                type: 'package',
                slug: $package->slug,
                title: $package->title,
                summary: $package->short_description,
                amount: $package->price_from,
                currency: $package->currency,
                image: $package->hero_image_url,
            ),
        ]);
    }

    public function startExperience(StartCheckoutRequest $request, string $slug, NetworkNgeniusGateway $gateway): RedirectResponse
    {
        $experience = Experience::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->startCheckout($request, $gateway, $experience);
    }

    public function startPackage(StartCheckoutRequest $request, string $slug, NetworkNgeniusGateway $gateway): RedirectResponse
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->startCheckout($request, $gateway, $package);
    }

    public function callback(
        Request $request,
        NetworkNgeniusGateway $gateway,
        BookingConfirmationService $confirmationService,
        NetworkPaymentSynchronizer $synchronizer,
    ): RedirectResponse {
        $transaction = PaymentTransaction::query()->findOrFail($request->integer('transaction'));

        if (! $transaction->gateway_order_ref) {
            return redirect()
                ->route('checkout.result', $transaction)
                ->with('error', 'Gateway order reference is missing for this transaction.');
        }

        $ok = $synchronizer->synchronizeFromGateway($transaction, $gateway, $confirmationService);

        if (! $ok) {
            Log::error('N-Genius fetchOrder failed after redirect.', [
                'transaction_id' => $transaction->id,
            ]);

            return redirect()
                ->route('checkout.result', $transaction)
                ->with(
                    'error',
                    'We could not confirm your payment with the bank yet. Please keep your reference and contact us if money was taken.',
                );
        }

        return redirect()->route('checkout.result', $transaction);
    }

    public function result(PaymentTransaction $transaction): InertiaResponse
    {
        $transaction->load('payable');

        $status = $transaction->status;

        $headline = match ($status) {
            'paid', 'authorized' => 'Booking payment received',
            'failed' => 'Payment was declined',
            'cancelled' => 'Payment cancelled',
            'refunded' => 'Payment refunded',
            default => 'Payment status',
        };

        $message = match ($status) {
            'paid', 'authorized' => 'Thank you. Confirmation emails are sent to the booker and guests on file where configured. Keep your reference for your records.',
            'failed' => 'Your bank or card issuer did not approve this charge. No payment was completed for this attempt. You can try again (same or another card) or reach us for alternatives such as bank transfer.',
            'cancelled' => 'You left or closed the payment page before finishing. You have not been charged. Use “Try checkout again” when you are ready, or return from the experience or package page.',
            'pending' => 'We have not received a final “paid” status yet. If you just completed payment, wait a few minutes and refresh this page. If it still shows pending, contact us with the reference below so we can trace it with the bank.',
            'refunded' => 'This transaction was marked as refunded. If that surprises you, contact us with your reference and we will help.',
            default => 'Review the details below. Contact us if you need help.',
        };

        $retryCheckoutUrl = $this->checkoutUrlForPayable($transaction->payable);

        return Inertia::render('Checkout/Result', [
            'seo' => [
                'title' => 'Payment Result',
                'description' => 'Review the outcome of your payment.',
            ],
            'payment' => [
                'reference' => $transaction->reference,
                'status' => $status,
                'headline' => $headline,
                'message' => $message,
                'isSuccess' => in_array($status, ['paid', 'authorized'], true),
                'amount' => "{$transaction->currency} ".number_format((float) $transaction->amount, 2),
                'customerName' => $transaction->customer_name,
                'itemTitle' => $transaction->payable?->title,
                'itemType' => class_basename($transaction->payable_type),
                'retryCheckoutUrl' => $retryCheckoutUrl,
            ],
        ]);
    }

    protected function checkoutUrlForPayable(?Model $payable): ?string
    {
        if ($payable instanceof Experience) {
            return route('checkout.experiences.show', $payable->slug);
        }

        if ($payable instanceof Package) {
            return route('checkout.packages.show', $payable->slug);
        }

        return null;
    }

    protected function startCheckout(StartCheckoutRequest $request, NetworkNgeniusGateway $gateway, Model $payable): RedirectResponse
    {
        $activityLogger = app(PaymentTransactionLogger::class);

        abort_if(! $payable->price_from, 422, 'This item is not currently configured for online payment.');

        $validated = $request->validated();
        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $payable::class,
            'payable_id' => $payable->getKey(),
            'user_id' => $request->user()?->id,
            'reference' => (string) Str::uuid(),
            'status' => 'pending',
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'] ?? null,
            'travel_date' => $validated['travel_date'] ?? null,
            'guest_count' => $validated['guest_count'] ?? null,
            'amount' => $payable->price_from,
            'amount_minor' => (int) round(((float) $payable->price_from) * 100),
            'currency' => $payable->currency,
        ]);

        $transaction->travelers()->createMany(
            collect($validated['traveler_contacts'])
                ->values()
                ->map(fn (array $traveler, int $index) => [
                    'position' => $index + 1,
                    'name' => $traveler['name'],
                    'email' => $traveler['email'],
                    'phone' => $traveler['phone'],
                ])
                ->all()
        );

        $activityLogger->record(
            $transaction,
            'checkout_session_created',
            'Customer submitted checkout; booking record and travelers saved.',
            [
                'reference' => $transaction->reference,
                'amount' => (string) $transaction->amount,
                'currency' => $transaction->currency,
                'guest_count' => $transaction->guest_count,
            ],
            $request->user(),
        );

        $callbackUrl = route('payments.network.callback', ['transaction' => $transaction->id]);
        $cancelUrl = $this->checkoutUrlForPayable($payable);

        try {
            $gatewayOrder = $gateway->createHostedOrder(
                $transaction,
                $callbackUrl,
                $cancelUrl,
            );
        } catch (\Throwable $exception) {
            Log::warning('N-Genius createHostedOrder failed.', [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ]);

            $transaction->update([
                'status' => 'failed',
                'notes' => $exception->getMessage(),
            ]);

            $activityLogger->record(
                $transaction->fresh(),
                'checkout_gateway_error',
                'Payment gateway did not return a hosted session.',
                ['message' => $exception->getMessage()],
                $request->user(),
            );

            return back()->with(
                'error',
                'Online payment is not available right now (gateway misconfiguration or outage). Please use contact / inquiry and we will assist you.',
            );
        }

        $transaction->update([
            'gateway_order_ref' => $gatewayOrder['order_ref'],
            'payment_url' => $gatewayOrder['payment_url'],
            'gateway_payload' => $gatewayOrder['payload'],
        ]);

        $transaction->refresh();

        $activityLogger->record(
            $transaction,
            'checkout_gateway_session_ready',
            'Hosted payment session created; customer redirected to N-Genius.',
            [
                'gateway_order_ref' => $gatewayOrder['order_ref'],
            ],
            $request->user(),
        );

        try {
            Mail::to($transaction->customer_email)->send(new CheckoutContinuePaymentMail($transaction));
        } catch (\Throwable $exception) {
            Log::warning('Checkout continue-payment email to customer failed.', [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ]);
        }

        try {
            app(AdminBookingNotifier::class)->checkoutPending($transaction);
        } catch (\Throwable $exception) {
            Log::warning('Checkout admin notification failed.', [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ]);
        }

        return redirect()->away($gatewayOrder['payment_url']);
    }

    protected function checkoutPayload(string $label, string $type, string $slug, string $title, ?string $summary, string $amount, string $currency, ?string $image): array
    {
        return [
            'label' => $label,
            'type' => $type,
            'slug' => $slug,
            'title' => $title,
            'summary' => $summary,
            'amount' => "{$currency} ".number_format((float) $amount, 2),
            'image' => $image,
        ];
    }
}
