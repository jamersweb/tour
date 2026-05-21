<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SiteSetting extends Model
{
    protected $fillable = [
        'site_name',
        'company_legal_name',
        'brand_kicker',
        'brand_name',
        'organization_type',
        'site_tagline',
        'website_url',
        'logo_url',
        'footer_logo_url',
        'social_links',
        'footer_description',
        'footer_build_notes',
        'footer_milestones',
        'contact_email',
        'contact_phone',
        'contact_phone_secondary',
        'contact_address',
        'whatsapp_number',
        'license_number',
        'google_reviews_url',
        'tripadvisor_reviews_url',
        'partner_proof',
        'interest_options',
        'home_hero_eyebrow',
        'home_hero_title',
        'home_hero_description',
        'home_primary_cta_label',
        'home_secondary_cta_label',
        'home_trust_heading',
        'home_trust_points',
        'home_collections_eyebrow',
        'home_collections_title',
        'home_featured_eyebrow',
        'home_featured_title',
    ];

    protected function casts(): array
    {
        return [
            'social_links' => 'array',
            'footer_build_notes' => 'array',
            'footer_milestones' => 'array',
            'partner_proof' => 'array',
            'interest_options' => 'array',
            'home_trust_points' => 'array',
        ];
    }

    public static function current(): self
    {
        return static::query()->firstOrCreate(
            ['id' => 1],
            [
                'site_name' => 'Acute Tourism',
                'company_legal_name' => 'Acute Tourism LLC',
                'brand_kicker' => 'Acute',
                'brand_name' => 'Tourism',
                'organization_type' => 'TravelAgency',
                'site_tagline' => 'Premium Dubai experiences rebuilt on Laravel, Vue, and Inertia.',
                'website_url' => 'https://new.acutetourism.org',
                'logo_url' => 'https://acutetourism.org/uploads/0000/6/2025/03/19/5.png',
                'footer_logo_url' => 'https://acutetourism.org/uploads/0000/6/2025/03/14/4-2.png',
                'social_links' => [
                    'https://www.instagram.com/acutetourism',
                    'https://www.facebook.com/acutetourism',
                    'https://www.tiktok.com/@acutetourism',
                    'https://ae.linkedin.com/company/acute-tourism-llc',
                ],
                'footer_description' => 'Exclusively Curated Holiday Experiences, crafted by destination experts, with itinerary designed to deliver a refined and effortless journey with luxury hotels, private transfers, bespoke desert safaris, and priority access to iconic landmarks.',
                'footer_build_notes' => ['Laravel 12', 'Vue 3 + Inertia', 'Network Payment Gateway'],
                'footer_milestones' => ['CMS-managed homepage', 'SEO migration prep', 'Production checkout'],
                'contact_email' => 'info@acutetourism.org',
                'contact_phone' => '(+971) 58 516 1554',
                'contact_phone_secondary' => null,
                'contact_address' => 'Shop 10, Kempinski Hotel & Residences, Palm Jumeirah, Dubai',
                'whatsapp_number' => '+971 58 516 1554',
                'license_number' => null,
                'google_reviews_url' => null,
                'tripadvisor_reviews_url' => 'https://www.tripadvisor.com/Attraction_Review-g295424-d33025321-Reviews-Acute_Tourism_LLC-Dubai_Emirate_of_Dubai.html',
                'partner_proof' => [
                    'Secure checkout via Network payment gateway',
                    'Office address at Kempinski Hotel & Residences, Palm Jumeirah',
                    'Hotels, transport, attractions, and visa-document support coordinated through one Acute team',
                ],
                'interest_options' => ['Private Desert', 'Yacht Experience', 'City Tour', 'Family Experience', 'Corporate Event', 'General Planning'],
                'home_hero_eyebrow' => 'Curated Dubai Experiences',
                'home_hero_title' => 'Private desert, yacht, and city experiences designed around service.',
                'home_hero_description' => 'Exclusively Curated Holiday Experiences, crafted by destination experts, with itinerary designed to deliver a refined and effortless journey with luxury hotels, private transfers, bespoke desert safaris, and priority access to iconic landmarks.',
                'home_primary_cta_label' => 'Explore Experiences',
                'home_secondary_cta_label' => 'Plan a Private Trip',
                'home_trust_heading' => 'Rebuild Focus',
                'home_trust_points' => [
                    'Curated private and premium experiences only',
                    'Fast concierge response via web and WhatsApp',
                    'Transparent inclusions, pickup, and cancellation details',
                    'Planned Network Payment Gateway integration for production checkout',
                ],
                'home_collections_eyebrow' => 'Core Collections',
                'home_collections_title' => 'Reduce catalog noise. Lead with curated high-intent entry points.',
                'home_featured_eyebrow' => 'Featured Direction',
                'home_featured_title' => 'Starter experience cards aligned with the premium repositioning.',
            ],
        );
    }
}
