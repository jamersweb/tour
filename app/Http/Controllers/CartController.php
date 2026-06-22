<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Package;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class CartController extends Controller
{
    public function index(Request $request): Response
    {
        return Inertia::render('Cart/Show', [
            'seo' => [
                'title' => 'Cart',
                'description' => 'Review selected experiences, packages, and tours before checkout.',
            ],
            'cart' => [
                'items' => $this->presentCartItems($request),
                'subtotal' => $this->formatMoney($this->cartSubtotal($request)),
                'checkoutUrl' => route('checkout.cart.show'),
            ],
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => ['required', Rule::in(['experience', 'package', 'tour'])],
            'slug' => ['required', 'string', 'max:180'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'adult_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'child_count' => ['nullable', 'integer', 'min:0', 'max:100'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'booking_option' => ['nullable', 'string', 'max:160'],
        ]);

        $payable = $this->resolvePayable($validated['type'], $validated['slug']);
        abort_if(! $payable || ! $payable->price_from, 404);

        if (($payable instanceof Experience || $payable instanceof Tour)
            && filled($validated['travel_date'] ?? null)
            && $this->isUnavailableDate($payable, (string) $validated['travel_date'])
        ) {
            return back()->withErrors([
                'travel_date' => 'This date is currently unavailable. Please choose another date.',
            ]);
        }

        $cart = $this->cart($request);
        $selectedBookingOption = $this->selectedBookingOption($payable, $validated['booking_option'] ?? null);
        $key = $this->cartKey($validated['type'], $validated['slug'], $selectedBookingOption['key'] ?? null);
        $adultCount = max(0, (int) ($validated['adult_count'] ?? ($cart[$key]['adult_count'] ?? 0)));
        $childCount = max(0, (int) ($validated['child_count'] ?? ($cart[$key]['child_count'] ?? 0)));
        $guestCount = $adultCount + $childCount;

        if ($guestCount < 1) {
            $guestCount = max($this->minimumGuestsForPayable($payable), (int) ($validated['guest_count'] ?? ($cart[$key]['guest_count'] ?? 1)));
            $adultCount = $guestCount;
            $childCount = 0;
        }

        $guestCount = $this->clampGuestsForPayable($payable, $guestCount);
        if ($payable instanceof Package && $adultCount + $childCount !== $guestCount) {
            $adultCount = $guestCount;
            $childCount = 0;
        }
        if ($adultCount + $childCount < $guestCount) {
            $adultCount = $guestCount - $childCount;
        }

        $cart[$key] = [
            'type' => $validated['type'],
            'slug' => $validated['slug'],
            'guest_count' => $guestCount,
            'adult_count' => $adultCount,
            'child_count' => $childCount,
            'travel_date' => $validated['travel_date'] ?? ($cart[$key]['travel_date'] ?? null),
            'booking_option' => $selectedBookingOption['key'] ?? null,
        ];

        $request->session()->put('cart.items', $cart);

        return back()->with('success', "{$payable->title} was added to your cart.");
    }

    public function update(Request $request, string $key): RedirectResponse
    {
        $validated = $request->validate([
            'guest_count' => ['required', 'integer', 'min:1', 'max:100'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $cart = $this->cart($request);
        abort_if(! isset($cart[$key]), 404);

        $payable = $this->resolvePayable((string) ($cart[$key]['type'] ?? ''), (string) ($cart[$key]['slug'] ?? ''));
        if (($payable instanceof Experience || $payable instanceof Tour)
            && filled($validated['travel_date'] ?? null)
            && $this->isUnavailableDate($payable, (string) $validated['travel_date'])
        ) {
            return back()->withErrors([
                'travel_date' => 'This date is currently unavailable. Please choose another date.',
            ]);
        }

        $cart[$key]['guest_count'] = $this->clampGuestsForPayable($payable, (int) $validated['guest_count']);
        $cart[$key]['travel_date'] = $validated['travel_date'] ?? null;

        $request->session()->put('cart.items', $cart);

        return back()->with('success', 'Cart item updated.');
    }

    public function destroy(Request $request, string $key): RedirectResponse
    {
        $cart = $this->cart($request);
        unset($cart[$key]);

        $request->session()->put('cart.items', $cart);

        return back()->with('success', 'Cart item removed.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart.items');

        return back()->with('success', 'Cart cleared.');
    }

    protected function presentCartItems(Request $request): array
    {
        return collect($this->cart($request))
            ->map(function (array $item, string $key) {
                $payable = $this->resolvePayable($item['type'], $item['slug']);

                if (! $payable || ! $payable->price_from) {
                    return null;
                }

                $guestCount = $this->clampGuestsForPayable($payable, (int) ($item['guest_count'] ?? 1));
                $selectedBookingOption = $this->selectedBookingOption($payable, $item['booking_option'] ?? null);
                $unitAmount = (float) ($selectedBookingOption['amountValue'] ?? $payable->price_from);
                $adultCount = max(0, (int) ($item['adult_count'] ?? $guestCount));
                $childCount = max(0, (int) ($item['child_count'] ?? 0));
                if ($payable instanceof Package && $adultCount + $childCount !== $guestCount) {
                    $adultCount = $guestCount;
                    $childCount = 0;
                }
                if ($adultCount + $childCount < $guestCount) {
                    $adultCount = $guestCount - $childCount;
                }
                $childUnitAmount = $this->childUnitAmount($payable, $unitAmount, $selectedBookingOption);
                $total = ($unitAmount * $adultCount) + ($childUnitAmount * $childCount);

                return [
                    'key' => $key,
                    'type' => $item['type'],
                    'label' => str($item['type'])->headline()->toString(),
                    'slug' => $item['slug'],
                    'title' => $payable->title,
                    'summary' => $payable->short_description,
                    'image' => $this->imageFor($payable),
                    'duration' => $payable->duration,
                    'location' => $payable->location,
                    'bookingOption' => $selectedBookingOption,
                    'guestCount' => $guestCount,
                    'adultCount' => $adultCount,
                    'childCount' => $childCount,
                    'travelDate' => $item['travel_date'] ?? null,
                    'unitAmount' => $this->formatMoney($unitAmount, $payable->currency),
                    'childUnitAmount' => $this->formatMoney($childUnitAmount, $payable->currency),
                    'lineTotal' => $this->formatMoney($total, $payable->currency),
                    'detailUrl' => $this->detailUrl($item['type'], $item['slug']),
                ];
            })
            ->filter()
            ->values()
            ->all();
    }

    protected function cartSubtotal(Request $request): float
    {
        return collect($this->cart($request))
            ->sum(function (array $item) {
                $payable = $this->resolvePayable($item['type'], $item['slug']);

                if (! $payable || ! $payable->price_from) {
                    return 0;
                }

                $selectedBookingOption = $this->selectedBookingOption($payable, $item['booking_option'] ?? null);
                $unitAmount = (float) ($selectedBookingOption['amountValue'] ?? $payable->price_from);
                $guestCount = $this->clampGuestsForPayable($payable, (int) ($item['guest_count'] ?? 1));
                $adultCount = max(0, (int) ($item['adult_count'] ?? $guestCount));
                $childCount = max(0, (int) ($item['child_count'] ?? 0));
                if ($payable instanceof Package && $adultCount + $childCount !== $guestCount) {
                    $adultCount = $guestCount;
                    $childCount = 0;
                }
                if ($adultCount + $childCount < $guestCount) {
                    $adultCount = $guestCount - $childCount;
                }
                $childUnitAmount = $this->childUnitAmount($payable, $unitAmount, $selectedBookingOption);

                return ($unitAmount * $adultCount) + ($childUnitAmount * $childCount);
            });
    }

    protected function cart(Request $request): array
    {
        return $request->session()->get('cart.items', []);
    }

    protected function resolvePayable(string $type, string $slug): ?Model
    {
        $model = match ($type) {
            'experience' => Experience::class,
            'package' => Package::class,
            'tour' => Tour::class,
        };

        return $model::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->first();
    }

    protected function cartKey(string $type, string $slug, ?string $bookingOption = null): string
    {
        return $bookingOption ? "{$type}:{$slug}:{$bookingOption}" : "{$type}:{$slug}";
    }

    protected function minimumGuestsForPayable(Model $payable): int
    {
        if ($payable instanceof Package) {
            return max(1, (int) ($payable->group_size_min ?: 1));
        }

        return 1;
    }

    protected function maximumGuestsForPayable(Model $payable): int
    {
        if ($payable instanceof Package && $payable->group_size_max) {
            return max($this->minimumGuestsForPayable($payable), (int) $payable->group_size_max);
        }

        return 100;
    }

    protected function clampGuestsForPayable(Model $payable, int $guestCount): int
    {
        return min(
            $this->maximumGuestsForPayable($payable),
            max($this->minimumGuestsForPayable($payable), $guestCount),
        );
    }

    protected function imageFor(Model $payable): ?string
    {
        if ($payable instanceof Package) {
            return $payable->hero_image_url ?: collect($payable->gallery_image_urls ?? [])->first();
        }

        return $payable->hero_image_url;
    }

    protected function detailUrl(string $type, string $slug): string
    {
        return match ($type) {
            'experience' => route('experiences.show', $slug),
            'package' => route('packages.show', $slug),
            'tour' => route('tours.show', $slug),
        };
    }

    protected function selectedBookingOption(Model $payable, ?string $key): ?array
    {
        $options = $this->pricedBookingOptions($payable);

        if ($options === []) {
            return null;
        }

        if (filled($key)) {
            $selected = collect($options)->firstWhere('key', $key);

            if ($selected) {
                return $selected;
            }
        }

        return $options[0];
    }

    protected function pricedBookingOptions(Model $payable): array
    {
        return collect($payable->booking_options ?? [])
            ->filter(fn ($option) => is_array($option) && filled($option['label'] ?? null) && is_numeric($option['price'] ?? null))
            ->values()
            ->map(function (array $option, int $index) use ($payable) {
                $label = trim((string) $option['label']);
                $amount = (float) $option['price'];
                $key = Str::slug($label) ?: "option-{$index}";

                return [
                    'key' => "{$key}-{$index}",
                    'label' => $label,
                    'description' => filled($option['description'] ?? null) ? trim((string) $option['description']) : null,
                    'amountValue' => $amount,
                    'amount' => $this->formatMoney($amount, $payable->currency ?: 'AED'),
                    'childAmountValue' => is_numeric($option['child_price'] ?? null) ? (float) $option['child_price'] : null,
                    'childAmount' => is_numeric($option['child_price'] ?? null) ? $this->formatMoney((float) $option['child_price'], $payable->currency ?: 'AED') : null,
                ];
            })
            ->all();
    }

    protected function formatMoney(float $amount, string $currency = 'AED'): string
    {
        return "{$currency} ".number_format($amount, 2);
    }

    protected function childUnitAmount(Model $payable, float $adultAmount, ?array $selectedBookingOption = null): float
    {
        if (is_numeric($selectedBookingOption['childAmountValue'] ?? null)) {
            return (float) $selectedBookingOption['childAmountValue'];
        }

        $childAmount = $payable->getAttribute('child_price_from');

        return is_numeric($childAmount) ? (float) $childAmount : $adultAmount;
    }

    protected function isUnavailableDate(Model $payable, string $date): bool
    {
        try {
            $selected = Carbon::parse($date)->toDateString();
        } catch (\Throwable) {
            return false;
        }

        if (in_array($selected, $payable->getAttribute('unavailable_dates') ?? [], true)) {
            return true;
        }

        foreach ($payable->getAttribute('unavailable_periods') ?? [] as $period) {
            if (! is_array($period) || blank($period['start'] ?? null) || blank($period['end'] ?? null)) {
                continue;
            }

            try {
                $start = Carbon::parse((string) $period['start'])->toDateString();
                $end = Carbon::parse((string) $period['end'])->toDateString();
            } catch (\Throwable) {
                continue;
            }

            if ($selected >= $start && $selected <= $end) {
                return true;
            }
        }

        return false;
    }
}
