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
        'social_links',
        'footer_description',
        'footer_build_notes',
        'footer_milestones',
        'contact_email',
        'contact_phone',
        'contact_address',
        'whatsapp_number',
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
                'website_url' => 'https://acutetourism.org',
                'logo_url' => 'https://acutetourism.org/uploads/0000/7/2025/04/12/logo-sin-texto.png',
                'social_links' => [
                    'https://www.instagram.com/acutetourism',
                    'https://www.facebook.com/acutetourism',
                ],
                'footer_description' => 'Premium Dubai experiences being rebuilt with a focused product architecture, stronger branding, and a concierge-first booking flow.',
                'footer_build_notes' => ['Laravel 12', 'Vue 3 + Inertia', 'Network Payment Gateway'],
                'footer_milestones' => ['CMS-managed homepage', 'SEO migration prep', 'Production checkout'],
                'contact_email' => 'info@acutetourism.org',
                'contact_phone' => '(+971) 4 409 6751',
                'contact_address' => 'Emaar Square, Boulevard Plaza Tower 2, Dubai',
                'whatsapp_number' => '+97144096751',
                'interest_options' => ['Private Desert', 'Yacht Experience', 'City Tour', 'Family Experience', 'Corporate Event', 'General Planning'],
                'home_hero_eyebrow' => 'Curated Dubai Experiences',
                'home_hero_title' => 'Private desert, yacht, and city experiences designed around service.',
                'home_hero_description' => 'Acute Tourism is being rebuilt as a premium Dubai experiences brand focused on polished operations, curated products, and concierge-led booking.',
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
