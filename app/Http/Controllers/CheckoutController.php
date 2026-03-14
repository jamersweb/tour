<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartCheckoutRequest;
use App\Models\Experience;
use App\Models\Package;
use App\Models\PaymentTransaction;
use App\Services\Payments\BookingConfirmationService;
use App\Services\Payments\NetworkNgeniusGateway;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class CheckoutController extends Controller
{
    public function experience(string $slug): Response
    {
        $experience = Experience::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        abort_if(! $experience->price_from, 404);

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

    public function package(string $slug): Response
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        abort_if(! $package->price_from, 404);

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

    public function callback(Request $request, NetworkNgeniusGateway $gateway, BookingConfirmationService $confirmationService): RedirectResponse
    {
        $transaction = PaymentTransaction::query()->findOrFail($request->integer('transaction'));

        if (! $transaction->gateway_order_ref) {
            return redirect()
                ->route('checkout.result', $transaction)
                ->with('error', 'Gateway order reference is missing for this transaction.');
        }

        $payload = $gateway->fetchOrder($transaction->gateway_order_ref);
        $paymentState = $this->resolveGatewayState($payload);

        $status = $this->mapGatewayStateToStatus($paymentState);

        $transaction->update([
            'status' => $status,
            'gateway_payment_ref' => $this->resolvePaymentReference($payload),
            'gateway_payload' => $payload,
            'paid_at' => in_array($status, ['paid', 'authorized'], true) ? now() : $transaction->paid_at,
        ]);

        if (in_array($status, ['paid', 'authorized'], true)) {
            $confirmationService->send($transaction->fresh());
        }

        return redirect()->route('checkout.result', $transaction);
    }

    public function result(PaymentTransaction $transaction): Response
    {
        $transaction->load('payable');

        return Inertia::render('Checkout/Result', [
            'seo' => [
                'title' => 'Payment Result',
                'description' => 'Review the outcome of your payment.',
            ],
            'payment' => [
                'reference' => $transaction->reference,
                'status' => $transaction->status,
                'amount' => "{$transaction->currency} ".number_format((float) $transaction->amount, 2),
                'customerName' => $transaction->customer_name,
                'itemTitle' => $transaction->payable?->title,
                'itemType' => class_basename($transaction->payable_type),
            ],
        ]);
    }

    protected function startCheckout(StartCheckoutRequest $request, NetworkNgeniusGateway $gateway, Model $payable): RedirectResponse
    {
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

        try {
            $gatewayOrder = $gateway->createHostedOrder(
                $transaction,
                route('payments.network.callback', ['transaction' => $transaction->id]),
            );
        } catch (\Throwable $exception) {
            $transaction->update([
                'status' => 'failed',
                'notes' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Payment gateway is not ready yet. Please complete your inquiry and the team can assist manually.');
        }

        $transaction->update([
            'gateway_order_ref' => $gatewayOrder['order_ref'],
            'payment_url' => $gatewayOrder['payment_url'],
            'gateway_payload' => $gatewayOrder['payload'],
        ]);

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

    protected function resolveGatewayState(array $payload): string
    {
        return Str::upper(
            Arr::get($payload, '_embedded.payment.0.state')
            ?? Arr::get($payload, 'state')
            ?? 'PENDING'
        );
    }

    protected function resolvePaymentReference(array $payload): ?string
    {
        $id = Arr::get($payload, '_embedded.payment.0._id');

        return $id ? Str::afterLast($id, ':') : null;
    }

    protected function mapGatewayStateToStatus(string $state): string
    {
        return match ($state) {
            'PURCHASED', 'CAPTURED' => 'paid',
            'AUTHORIZED' => 'authorized',
            'FAILED', 'DECLINED' => 'failed',
            'CANCELLED', 'CANCELED' => 'cancelled',
            default => 'pending',
        };
    }
}
