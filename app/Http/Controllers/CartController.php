<?php

namespace App\Http\Controllers;

use App\Models\Experience;
use App\Models\Package;
use App\Models\Tour;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
        ]);

        $payable = $this->resolvePayable($validated['type'], $validated['slug']);
        abort_if(! $payable || ! $payable->price_from, 404);

        $cart = $this->cart($request);
        $key = $this->cartKey($validated['type'], $validated['slug']);
        $guestCount = max(1, (int) ($validated['guest_count'] ?? ($cart[$key]['guest_count'] ?? 1)));

        $cart[$key] = [
            'type' => $validated['type'],
            'slug' => $validated['slug'],
            'guest_count' => $guestCount,
            'travel_date' => $validated['travel_date'] ?? ($cart[$key]['travel_date'] ?? null),
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

        $cart[$key]['guest_count'] = (int) $validated['guest_count'];
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

                $guestCount = max(1, (int) ($item['guest_count'] ?? 1));
                $unitAmount = (float) $payable->price_from;
                $total = $unitAmount * $guestCount;

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
                    'guestCount' => $guestCount,
                    'travelDate' => $item['travel_date'] ?? null,
                    'unitAmount' => $this->formatMoney($unitAmount, $payable->currency),
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

                return (float) $payable->price_from * max(1, (int) ($item['guest_count'] ?? 1));
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

    protected function cartKey(string $type, string $slug): string
    {
        return "{$type}:{$slug}";
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

    protected function formatMoney(float $amount, string $currency = 'AED'): string
    {
        return "{$currency} ".number_format($amount, 2);
    }
}
