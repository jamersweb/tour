<?php

namespace App\Http\Controllers;

use App\Http\Requests\StartCheckoutRequest;
use App\Mail\CheckoutContinuePaymentMail;
use App\Models\Experience;
use App\Models\Package;
use App\Models\PaymentTransaction;
use App\Models\Tour;
use App\Services\AdminBookingNotifier;
use App\Services\Payments\BookingConfirmationService;
use App\Services\Payments\NetworkNgeniusGateway;
use App\Services\Payments\NetworkPaymentSynchronizer;
use App\Services\Payments\PaymentTransactionLogger;
use App\Support\NetworkPayments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Client\RequestException;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;
use Symfony\Component\HttpFoundation\Response;

class CheckoutController extends Controller
{
    public function experience(Request $request, string $slug): InertiaResponse|RedirectResponse
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
                defaults: $this->checkoutDefaults($request, 2),
                supportsTourPreferences: true,
                preferenceOptions: $this->bookingPreferenceOptions($experience),
            ),
        ]);
    }

    public function package(Request $request, string $slug): InertiaResponse|RedirectResponse
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
                defaults: $this->checkoutDefaults($request),
            ),
        ]);
    }

    public function tour(Request $request, string $slug): InertiaResponse|RedirectResponse
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        abort_if(! $tour->price_from, 404);

        return Inertia::render('Checkout/Show', [
            'seo' => [
                'title' => 'Checkout',
                'description' => "Complete checkout for {$tour->title}.",
            ],
            'checkout' => $this->checkoutPayload(
                label: 'Tour Checkout',
                type: 'tour',
                slug: $tour->slug,
                title: $tour->title,
                summary: $tour->short_description,
                amount: $tour->price_from,
                currency: $tour->currency,
                image: $tour->hero_image_url,
                defaults: $this->checkoutDefaults($request),
                supportsTourPreferences: true,
                preferenceOptions: $this->bookingPreferenceOptions($tour),
            ),
        ]);
    }

    public function cart(Request $request): InertiaResponse|RedirectResponse
    {
        $cart = $this->cartPayload($request);

        if ($cart['items'] === []) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        return Inertia::render('Checkout/Show', [
            'seo' => [
                'title' => 'Cart Checkout',
                'description' => 'Complete checkout for all items in your cart.',
            ],
            'checkout' => [
                'label' => 'Cart Checkout',
                'type' => 'cart',
                'slug' => 'cart',
                'title' => 'Complete checkout for your cart',
                'summary' => 'One payment will cover every item currently in your cart.',
                'unitAmountValue' => $cart['total'],
                'currency' => $cart['currency'],
                'amount' => $this->formatMoney($cart['total'], $cart['currency']),
                'image' => $cart['items'][0]['image'] ?? null,
                'defaults' => [
                    'guest_count' => $cart['guest_count'],
                    'travel_date' => null,
                ],
                'isCart' => true,
                'items' => $cart['items'],
            ],
        ]);
    }

    public function startExperience(StartCheckoutRequest $request, string $slug, NetworkNgeniusGateway $gateway): Response
    {
        $experience = Experience::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->startCheckout($request, $gateway, $experience);
    }

    public function startPackage(StartCheckoutRequest $request, string $slug, NetworkNgeniusGateway $gateway): Response
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->startCheckout($request, $gateway, $package);
    }

    public function startTour(StartCheckoutRequest $request, string $slug, NetworkNgeniusGateway $gateway): Response
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return $this->startCheckout($request, $gateway, $tour);
    }

    public function startCart(StartCheckoutRequest $request, NetworkNgeniusGateway $gateway): Response
    {
        $cart = $this->cartPayload($request);

        if ($cart['items'] === []) {
            return back()->with('error', 'Your cart is empty.');
        }

        return $this->startCheckout(
            $request,
            $gateway,
            $cart['payable'],
            [
                'amount' => $cart['total'],
                'currency' => $cart['currency'],
                'guest_count' => $cart['guest_count'],
                'travel_date' => null,
                'cart_items' => $cart['items'],
                'cancel_url' => route('cart.index'),
                'success_message_context' => 'cart',
            ],
        );
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

        if (is_array($transaction->cart_items) && $transaction->cart_items !== [] && in_array($transaction->fresh()->status, ['paid', 'authorized'], true)) {
            $request->session()->forget('cart.items');
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

        $isCartCheckout = is_array($transaction->cart_items) && $transaction->cart_items !== [];
        $retryCheckoutUrl = $isCartCheckout ? route('cart.index') : $this->checkoutUrlForPayable($transaction->payable);
        $itemTitle = $isCartCheckout
            ? count($transaction->cart_items).' cart item'.(count($transaction->cart_items) === 1 ? '' : 's')
            : $transaction->payable?->title;

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
                'itemTitle' => $itemTitle,
                'itemType' => $isCartCheckout ? 'Cart' : class_basename($transaction->payable_type),
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

        if ($payable instanceof Tour) {
            return route('checkout.tours.show', $payable->slug);
        }

        return null;
    }

    protected function startCheckout(StartCheckoutRequest $request, NetworkNgeniusGateway $gateway, Model $payable, array $overrides = []): Response
    {
        $activityLogger = app(PaymentTransactionLogger::class);

        abort_if(! $payable->price_from, 422, 'This item is not currently configured for online payment.');

        if (! NetworkPayments::isCheckoutReady()) {
            $message = $payable instanceof Experience
                ? 'Online payment is not available right now. Please plan this experience or contact us to complete your booking.'
                : 'Online payment is not available right now. Please contact us to book this package.';

            return back()->with('error', $message);
        }

        $validated = $request->validated();
        $guestCount = (int) ($overrides['guest_count'] ?? ($validated['guest_count'] ?? 1));
        $guestCount = $payable instanceof Package ? max(2, $guestCount) : max(1, $guestCount);
        $unitAmount = (float) $payable->price_from;
        $totalAmount = round((float) ($overrides['amount'] ?? ($unitAmount * max(1, $guestCount))), 2);
        $currency = (string) ($overrides['currency'] ?? $payable->currency);
        $preferenceNotes = collect([
            'Tour option' => $validated['tour_option'] ?? null,
            'Preferred time' => $validated['preferred_time'] ?? null,
            'Preferred language' => $validated['preferred_language'] ?? null,
            'Special request' => $validated['special_request'] ?? null,
        ])
            ->filter(fn (?string $value) => filled($value))
            ->map(fn (string $value, string $label) => "{$label}: {$value}")
            ->implode("\n");

        $transaction = PaymentTransaction::query()->create([
            'payable_type' => $payable::class,
            'payable_id' => $payable->getKey(),
            'user_id' => $request->user()?->id,
            'reference' => (string) Str::uuid(),
            'status' => 'pending',
            'customer_name' => $validated['name'],
            'customer_email' => $validated['email'],
            'customer_phone' => $validated['phone'] ?? null,
            'travel_date' => array_key_exists('travel_date', $overrides)
                ? $overrides['travel_date']
                : ($validated['travel_date'] ?? null),
            'guest_count' => $guestCount,
            'cart_items' => $overrides['cart_items'] ?? null,
            'amount' => $totalAmount,
            'amount_minor' => (int) round($totalAmount * 100),
            'currency' => $currency,
            'notes' => $preferenceNotes ?: null,
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
        $cancelUrl = $overrides['cancel_url'] ?? $this->checkoutUrlForPayable($payable);

        try {
            $gatewayOrder = $gateway->createHostedOrder(
                $transaction,
                $callbackUrl,
                $cancelUrl,
            );
        } catch (\Throwable $exception) {
            $logContext = [
                'transaction_id' => $transaction->id,
                'message' => $exception->getMessage(),
            ];
            if ($exception instanceof RequestException && $exception->response) {
                $logContext['gateway_status'] = $exception->response->status();
                $logContext['gateway_body'] = $exception->response->body();
            }
            Log::warning('N-Genius createHostedOrder failed.', $logContext);

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

        return Inertia::location($gatewayOrder['payment_url']);
    }

    protected function checkoutPayload(
        string $label,
        string $type,
        string $slug,
        string $title,
        ?string $summary,
        string $amount,
        string $currency,
        ?string $image,
        array $defaults = [],
        bool $supportsTourPreferences = false,
        array $preferenceOptions = [],
    ): array
    {
        $unitAmount = (float) $amount;

        return [
            'label' => $label,
            'type' => $type,
            'slug' => $slug,
            'title' => $title,
            'summary' => $summary,
            'unitAmountValue' => $unitAmount,
            'currency' => $currency,
            'amount' => "{$currency} ".number_format($unitAmount, 2),
            'image' => $image,
            'defaults' => $defaults,
            'supportsTourPreferences' => $supportsTourPreferences,
            'preferenceOptions' => $preferenceOptions,
        ];
    }

    protected function bookingPreferenceOptions(Model $payable): array
    {
        return [
            'times' => $this->cleanPreferenceOptions($payable->preferred_time_options ?? []),
            'languages' => $this->cleanPreferenceOptions($payable->preferred_language_options ?? []),
            'tourOptions' => $this->cleanPreferenceOptions($payable->tour_options ?? []),
        ];
    }

    protected function cleanPreferenceOptions(?array $options): array
    {
        return collect($options ?? [])
            ->filter(fn ($value) => is_string($value) && filled($value))
            ->map(fn (string $value) => trim($value))
            ->unique()
            ->values()
            ->all();
    }

    protected function checkoutDefaults(Request $request, int $minimumGuests = 1): array
    {
        return [
            'guest_count' => max($minimumGuests, min(100, $request->integer('guest_count') ?: $minimumGuests)),
            'travel_date' => $request->query('travel_date'),
        ];
    }

    protected function cartPayload(Request $request): array
    {
        $items = [];
        $total = 0.0;
        $guestCount = 0;
        $currency = null;
        $firstPayable = null;

        foreach ($request->session()->get('cart.items', []) as $item) {
            $payable = $this->resolveCartPayable($item['type'] ?? '', $item['slug'] ?? '');

            if (! $payable || ! $payable->price_from) {
                continue;
            }

            $itemCurrency = $payable->currency ?: 'AED';
            if ($currency !== null && $currency !== $itemCurrency) {
                abort(422, 'Cart checkout requires all items to use the same currency.');
            }

            $currency ??= $itemCurrency;
            $firstPayable ??= $payable;
            $lineGuestCount = $payable instanceof Package
                ? max(2, (int) ($item['guest_count'] ?? 2))
                : max(1, (int) ($item['guest_count'] ?? 1));
            $unitAmount = (float) $payable->price_from;
            $lineTotal = round($unitAmount * $lineGuestCount, 2);
            $total += $lineTotal;
            $guestCount += $lineGuestCount;

            $items[] = [
                'type' => $item['type'],
                'slug' => $item['slug'],
                'title' => $payable->title,
                'summary' => $payable->short_description,
                'image' => $this->imageForPayable($payable),
                'duration' => $payable->duration,
                'location' => $payable->location,
                'travelDate' => $item['travel_date'] ?? null,
                'guestCount' => $lineGuestCount,
                'unitAmount' => $unitAmount,
                'unitAmountFormatted' => $this->formatMoney($unitAmount, $itemCurrency),
                'lineTotal' => $lineTotal,
                'lineTotalFormatted' => $this->formatMoney($lineTotal, $itemCurrency),
            ];
        }

        return [
            'items' => $items,
            'total' => round($total, 2),
            'currency' => $currency ?: 'AED',
            'guest_count' => max(1, $guestCount),
            'payable' => $firstPayable,
        ];
    }

    protected function resolveCartPayable(string $type, string $slug): ?Model
    {
        $model = match ($type) {
            'experience' => Experience::class,
            'package' => Package::class,
            'tour' => Tour::class,
            default => null,
        };

        if (! $model) {
            return null;
        }

        return $model::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    protected function imageForPayable(Model $payable): ?string
    {
        if ($payable instanceof Package) {
            return $payable->hero_image_url ?: collect($payable->gallery_image_urls ?? [])->first();
        }

        return $payable->hero_image_url;
    }

    protected function formatMoney(float $amount, string $currency = 'AED'): string
    {
        return "{$currency} ".number_format($amount, 2);
    }
}
