<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
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
            $logoUrl = 'https://acutetourism.org/uploads/0000/6/2025/03/19/5.png';
        }

        $footerLogoUrl = $settings->footer_logo_url;

        if (! $footerLogoUrl || str_ends_with($footerLogoUrl, '/logo.png')) {
            $footerLogoUrl = 'https://acutetourism.org/uploads/0000/6/2025/03/14/4-2.png';
        }

        return [
            ...parent::share($request),
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
                    'title' => $settings->site_name,
                    'description' => 'Exclusively curated holiday experiences in Dubai and the UAE — luxury stays, private transfers, and concierge-led planning.',
                    'image' => $logoUrl,
                ],
                'organization' => [
                    'name' => $settings->site_name,
                    'legalName' => $settings->company_legal_name ?: $settings->site_name,
                    'type' => $settings->organization_type ?: 'Organization',
                    'url' => $settings->website_url ?: config('app.url'),
                    'logo' => $logoUrl,
                    'socialLinks' => $settings->social_links ?? [],
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
                'interestOptions' => $settings->interest_options ?? [],
                'footer' => [
                    'description' => $settings->footer_description,
                    'legalName' => $settings->company_legal_name ?: $settings->site_name,
                    'website' => $settings->website_url ?: config('app.url'),
                    'socialLinks' => collect($settings->social_links ?? [])
                        ->filter(fn ($url) => filled($url))
                        ->map(function ($url) {
                            $host = parse_url($url, PHP_URL_HOST) ?: $url;
                            $label = str($host)
                                ->replace('www.', '')
                                ->before('.')
                                ->headline()
                                ->toString();

                            return [
                                'label' => $label,
                                'href' => $url,
                            ];
                        })
                        ->values()
                        ->all(),
                ],
                'footerNavigation' => [
                    ['label' => 'Experiences', 'href' => route('experiences.index')],
                    ['label' => 'Packages', 'href' => route('packages.index')],
                    ['label' => 'Schengen Visa', 'href' => route('visa.schengen')],
                    ['label' => 'About', 'href' => route('about')],
                    ['label' => 'Corporate Events', 'href' => route('corporate-events')],
                    ['label' => 'Contact', 'href' => route('contact')],
                ],
                'primaryNavigation' => [
                    ['label' => 'Home', 'href' => route('home')],
                    [
                        'label' => 'Explore',
                        'children' => [
                            ['label' => 'Experiences', 'href' => route('experiences.index')],
                            ['label' => 'Packages', 'href' => route('packages.index')],
                            ['label' => 'Schengen Visa', 'href' => route('visa.schengen')],
                        ],
                    ],
                    [
                        'label' => 'Company',
                        'children' => [
                            ['label' => 'About', 'href' => route('about')],
                            ['label' => 'Corporate Events', 'href' => route('corporate-events')],
                        ],
                    ],
                    [
                        'label' => 'Resources',
                        'children' => [
                            ['label' => 'Journal', 'href' => route('journal')],
                            ['label' => 'FAQ', 'href' => route('faq')],
                            ['label' => 'Contact', 'href' => route('contact')],
                        ],
                    ],
                ],
            ],
        ];
    }
}
