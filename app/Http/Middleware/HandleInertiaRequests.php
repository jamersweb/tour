<?php

namespace App\Http\Middleware;

use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    public function share(Request $request): array
    {
        $settings = SiteSetting::current();
        $logoUrl = $settings->logo_url;

        if (! $logoUrl || str_ends_with($logoUrl, '/logo.png')) {
            $logoUrl = 'https://acutetourism.org/uploads/0000/7/2025/04/12/logo-sin-texto.png';
        }

        return [
            ...parent::share($request),
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
                'homeUrl' => route('home'),
                'appUrl' => config('app.url'),
                'currentUrl' => $request->url(),
                'defaultMeta' => [
                    'title' => $settings->site_name,
                    'description' => 'Premium Dubai experiences with curated desert, yacht, city, and private itineraries.',
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
                        'address' => $settings->contact_address,
                        'whatsappNumber' => $settings->whatsapp_number,
                    ],
                ],
                'contact' => [
                    'email' => $settings->contact_email,
                    'phone' => $settings->contact_phone,
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
                    ['label' => 'About', 'href' => route('about')],
                    ['label' => 'Corporate Events', 'href' => route('corporate-events')],
                    ['label' => 'Journal', 'href' => route('journal')],
                    ['label' => 'Contact', 'href' => route('contact')],
                ],
                'primaryNavigation' => [
                    ['label' => 'Home', 'href' => route('home')],
                    ['label' => 'Experiences', 'href' => route('experiences.index')],
                    ['label' => 'Packages', 'href' => route('packages.index')],
                    ['label' => 'About', 'href' => route('about')],
                    ['label' => 'Corporate Events', 'href' => route('corporate-events')],
                    ['label' => 'Journal', 'href' => route('journal')],
                    ['label' => 'FAQ', 'href' => route('faq')],
                    ['label' => 'Contact', 'href' => route('contact')],
                ],
            ],
        ];
    }
}
