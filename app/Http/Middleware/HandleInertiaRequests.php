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
                'appUrl' => config('app.url'),
                'currentUrl' => $request->url(),
                'defaultMeta' => [
                    'title' => $settings->site_name,
                    'description' => 'Premium Dubai experiences with curated desert, yacht, city, and private itineraries.',
                    'image' => $settings->logo_url,
                ],
                'organization' => [
                    'name' => $settings->site_name,
                    'legalName' => $settings->company_legal_name ?: $settings->site_name,
                    'type' => $settings->organization_type ?: 'Organization',
                    'url' => $settings->website_url ?: config('app.url'),
                    'logo' => $settings->logo_url,
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
                    'buildNotes' => $settings->footer_build_notes ?? [],
                    'milestones' => $settings->footer_milestones ?? [],
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
