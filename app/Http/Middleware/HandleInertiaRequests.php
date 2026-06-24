<?php

namespace App\Http\Middleware;

use App\Models\Collection;
use App\Models\SiteSetting;
use App\Support\MediaUrl;
use App\Support\NetworkPayments;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        $manifest = public_path('build/manifest.json');

        if (is_file($manifest)) {
            return (string) @filemtime($manifest);
        }

        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $settings = SiteSetting::current();
        $logoUrl = $settings->logo_url;

        if (! $logoUrl || str_ends_with($logoUrl, '/logo.png')) {
            $logoUrl = '/legacy-media/uploads/0000/6/2025/03/19/5.png';
        }

        $footerLogoUrl = $settings->footer_logo_url;

        if (! $footerLogoUrl || str_ends_with($footerLogoUrl, '/logo.png')) {
            $footerLogoUrl = '/legacy-media/uploads/0000/6/2025/03/14/4-2.png';
        }

        $logoUrl = MediaUrl::normalize($logoUrl);
        $footerLogoUrl = MediaUrl::normalize($footerLogoUrl);
        $socialUrls = $this->socialUrls($settings->social_links ?? []);
        $footerSocialLinks = $this->footerSocialLinks($socialUrls);

        return [
            ...parent::share($request),
            'cart' => [
                'count' => fn () => collect($request->session()->get('cart.items', []))
                    ->sum(fn (array $item) => max(1, (int) ($item['guest_count'] ?? 1))),
                'url' => route('cart.index'),
            ],
            'payments' => [
                'networkEnabled' => (bool) config('payments.network.enabled'),
                'networkCheckoutReady' => NetworkPayments::isCheckoutReady(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
                'error' => fn () => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn () => $request->user() ? [
                    'name' => $request->user()->name,
                    'email' => $request->user()->email,
                    'dashboardUrl' => route('account.dashboard'),
                ] : null,
            ],
            'site' => [
                'name' => $settings->site_name,
                'brandKicker' => $settings->brand_kicker,
                'brandName' => $settings->brand_name,
                'tagline' => $settings->site_tagline,
                'logoUrl' => $logoUrl,
                'footerLogoUrl' => $footerLogoUrl,
                'homeUrl' => route('home'),
                'appUrl' => config('app.url'),
                'currentUrl' => $request->url(),
                'defaultMeta' => [
                    'title' => 'Acute Tourism | Dubai Tours, Holiday Packages & Visa Assistance',
                    'description' => 'Book Dubai tours, holiday packages, attraction tickets, panoramic bus experiences, and outbound visa assistance with Acute Tourism in the UAE.',
                    'image' => $logoUrl,
                ],
                'organization' => [
                    'name' => $settings->site_name,
                    'legalName' => $settings->company_legal_name ?: $settings->site_name,
                    'type' => $settings->organization_type ?: 'Organization',
                    'url' => $settings->website_url ?: config('app.url'),
                    'logo' => $logoUrl,
                    'socialLinks' => $socialUrls,
                    'contact' => [
                        'email' => $settings->contact_email,
                        'phone' => $settings->contact_phone,
                        'phoneSecondary' => $settings->contact_phone_secondary,
                        'address' => $settings->contact_address,
                        'whatsappNumber' => $settings->whatsapp_number,
                    ],
                ],
                'contact' => [
                    'email' => $settings->contact_email,
                    'phone' => $settings->contact_phone,
                    'phoneSecondary' => $settings->contact_phone_secondary,
                    'address' => $settings->contact_address,
                    'whatsappNumber' => $settings->whatsapp_number,
                ],
                'trust' => [
                    'travelerCount' => '2,500+ happy travelers',
                    'yearsInDubai' => '12 years in Dubai',
                    'licenseNumber' => $settings->license_number,
                    'licenseText' => $settings->license_number
                        ? "DTCM / DED license {$settings->license_number}"
                        : 'Dubai licensed operator - license number available on request',
                    'responseTime' => 'We respond within 2 hours on WhatsApp',
                    'googleReviewsUrl' => $settings->google_reviews_url,
                    'tripadvisorReviewsUrl' => $settings->tripadvisor_reviews_url,
                    'partnerProof' => $settings->partner_proof ?? [],
                ],
                'interestOptions' => $settings->interest_options ?? [],
                'footer' => [
                    'description' => $settings->footer_description,
                    'legalName' => $settings->company_legal_name ?: $settings->site_name,
                    'website' => $settings->website_url ?: config('app.url'),
                    'socialLinks' => $footerSocialLinks,
                ],
                'footerNavigation' => [
                    ['label' => 'Tours & Tickets', 'href' => route('experiences.index')],
                    ['label' => 'Holiday Packages', 'href' => route('packages.index')],
                    ['label' => 'Visa Services', 'href' => route('visa.index')],
                    ['label' => 'Panoramic Bus', 'href' => route('bus-tour')],
                    ['label' => 'Corporate Events', 'href' => route('corporate-events')],
                    ['label' => 'About us', 'href' => route('about')],
                    ['label' => 'Blog', 'href' => route('blog')],
                    ['label' => 'Cancellation Policy', 'href' => route('cancellation-policy')],
                    ['label' => 'Terms & Conditions', 'href' => route('terms-and-conditions')],
                    ['label' => 'Privacy Policy', 'href' => route('privacy-policy')],
                    ['label' => 'Earn With Tourgrat', 'href' => route('partner-with-us')],
                    ['label' => 'Contact', 'href' => route('contact')],
                ],
                'primaryNavigation' => [
                    [
                        'label' => 'Tours & Tickets',
                        'href' => route('experiences.index'),
                        'children' => $this->toursAndTicketsNavigation(),
                    ],
                    ['label' => 'Holiday Packages', 'href' => route('packages.index')],
                    ['label' => 'Visa Services', 'href' => route('visa.index')],
                    ['label' => 'Panoramic Bus', 'href' => route('bus-tour')],
                    ['label' => 'Contact', 'href' => route('contact')],
                ],
                'mobileNavigation' => [
                    [
                        'label' => 'Tours & Tickets',
                        'href' => route('experiences.index'),
                        'children' => $this->toursAndTicketsNavigation(),
                    ],
                    ['label' => 'Holiday Packages', 'href' => route('packages.index')],
                    ['label' => 'Visa Services', 'href' => route('visa.index')],
                    ['label' => 'Panoramic Bus', 'href' => route('bus-tour')],
                    ['label' => 'Contact', 'href' => route('contact')],
                ],
            ],
        ];
    }

    /**
     * @param  array<int, string>  $configuredUrls
     * @return array<int, string>
     */
    private function socialUrls(array $configuredUrls): array
    {
        $urls = collect($configuredUrls)
            ->filter(fn ($url) => filled($url))
            ->map(fn ($url) => (string) $url)
            ->values();
        $platforms = $urls
            ->map(fn (string $url) => $this->socialPlatform($url))
            ->filter()
            ->all();

        foreach ($this->defaultSocialUrls() as $platform => $url) {
            if (! in_array($platform, $platforms, true)) {
                $urls->push($url);
            }
        }

        return $urls->unique()->values()->all();
    }

    /**
     * @return array<int, array{label: string, href: string}>
     */
    private function toursAndTicketsNavigation(): array
    {
        $menuSlugs = $this->toursAndTicketsMenuSlugs();

        $groups = Collection::query()
            ->where('is_featured', true)
            ->where(function ($query) use ($menuSlugs): void {
                $query
                    ->where(fn ($locationQuery) => $locationQuery
                        ->where('collection_group', 'location')
                        ->whereIn('slug', $menuSlugs['location']))
                    ->orWhere(fn ($activityQuery) => $activityQuery
                        ->where('collection_group', 'activity')
                        ->whereIn('slug', $menuSlugs['activity']));
            })
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['name', 'slug', 'collection_group'])
            ->groupBy('collection_group');

        $navigation = [
            ['label' => 'All Tours & Tickets', 'href' => route('experiences.index')],
        ];

        if ($groups->has('location')) {
            $navigation[] = [
                'label' => 'By Location',
                'href' => route('experiences.index'),
                'children' => $this->collectionNavigationItems($groups->get('location'), 'location'),
            ];
        }

        if ($groups->has('activity')) {
            $navigation[] = [
                'label' => 'By Activity Type',
                'href' => route('experiences.index'),
                'children' => $this->collectionNavigationItems($groups->get('activity'), 'activity'),
            ];
        }

        return $navigation;
    }

    /**
     * @param  \Illuminate\Support\Collection<int, Collection>  $collections
     * @return array<int, array{label: string, href: string}>
     */
    private function collectionNavigationItems($collections, string $group): array
    {
        return $collections
            ->map(fn (Collection $collection): array => [
                'label' => $collection->name,
                'href' => $group === 'location'
                    ? route('experiences.location', $collection->slug)
                    : route('experiences.category', $collection->slug),
            ])
            ->values()
            ->all();
    }

    /**
     * @return array{location: array<int, string>, activity: array<int, string>}
     */
    private function toursAndTicketsMenuSlugs(): array
    {
        return [
            'location' => ['dubai', 'abu-dhabi', 'other-emirates'],
            'activity' => ['entry-tickets', 'desert-safari', 'city-tours', 'water-sports', 'water-parks', 'theme-parks', 'yacht-cruises'],
        ];
    }

    /**
     * @param  array<int, string>  $urls
     * @return array<int, array{label: string, href: string}>
     */
    private function footerSocialLinks(array $urls): array
    {
        return collect($urls)
            ->map(function (string $url) {
                $platform = $this->socialPlatform($url);
                $host = parse_url($url, PHP_URL_HOST) ?: $url;
                $label = match ($platform) {
                    'instagram' => 'Instagram',
                    'facebook' => 'Facebook',
                    'tiktok' => 'TikTok',
                    'linkedin' => 'LinkedIn',
                    default => str($host)
                        ->replace('www.', '')
                        ->before('.')
                        ->headline()
                        ->toString(),
                };

                return [
                    'label' => $label,
                    'href' => $url,
                ];
            })
            ->values()
            ->all();
    }

    /**
     * @return array<string, string>
     */
    private function defaultSocialUrls(): array
    {
        return [
            'instagram' => 'https://www.instagram.com/acutetourism',
            'facebook' => 'https://www.facebook.com/acutetourism',
            'tiktok' => 'https://www.tiktok.com/@acutetourism',
            'linkedin' => 'https://ae.linkedin.com/company/acute-tourism-llc',
        ];
    }

    private function socialPlatform(string $url): ?string
    {
        $value = strtolower($url);

        foreach (array_keys($this->defaultSocialUrls()) as $platform) {
            if (str_contains($value, $platform)) {
                return $platform;
            }
        }

        return null;
    }
}
