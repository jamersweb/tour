<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\UploadPath;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    protected $fillable = [
        'page_key',
        'admin_title',
        'route_uri',
        'is_active',
        'seo_title',
        'seo_description',
        'hero_eyebrow',
        'hero_title',
        'hero_description',
        'hero_image_path',
        'hero_image_url',
        'primary_cta_label',
        'primary_cta_url',
        'secondary_cta_label',
        'secondary_cta_url',
        'sidebar_label',
        'sidebar_title',
        'sidebar_body',
        'sidebar_items',
        'metrics',
        'content_sections',
        'faqs',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sidebar_items' => 'array',
            'metrics' => 'array',
            'content_sections' => 'array',
            'faqs' => 'array',
        ];
    }

    public static function forKey(string $key, array $defaults = []): self
    {
        return static::query()->firstOrCreate(
            ['page_key' => $key],
            static::recordDefaults($key, $defaults),
        );
    }

    public static function ensureDefaultRecords(): void
    {
        foreach (static::defaultPageDefinitions() as $key => $defaults) {
            static::forKey($key, $defaults);
        }
    }

    public function getHeroImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setHeroImagePathAttribute(mixed $value): void
    {
        $this->attributes['hero_image_path'] = UploadPath::normalize($value);
    }

    public function getHeroImageResolvedUrlAttribute(): ?string
    {
        if ($this->hero_image_path) {
            return MediaUrl::upload($this->hero_image_path);
        }

        return MediaUrl::normalize($this->hero_image_url);
    }

    public function publicPayload(): array
    {
        return [
            'key' => $this->page_key,
            'title' => $this->admin_title,
            'routeUri' => $this->route_uri,
            'isActive' => $this->is_active,
            'seo' => [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ],
            'hero' => [
                'eyebrow' => $this->hero_eyebrow,
                'title' => $this->hero_title,
                'description' => $this->hero_description,
                'imageUrl' => $this->hero_image_resolved_url,
            ],
            'primaryCta' => [
                'label' => $this->primary_cta_label,
                'url' => $this->primary_cta_url,
            ],
            'secondaryCta' => [
                'label' => $this->secondary_cta_label,
                'url' => $this->secondary_cta_url,
            ],
            'sidebar' => [
                'label' => $this->sidebar_label,
                'title' => $this->sidebar_title,
                'body' => $this->sidebar_body,
                'items' => $this->sidebar_items ?? [],
            ],
            'metrics' => $this->metrics ?? [],
            'sections' => $this->content_sections ?? [],
            'faqs' => $this->faqs ?? [],
        ];
    }

    public static function recordDefaults(string $key, array $defaults = []): array
    {
        return [
            'admin_title' => $defaults['admin_title'] ?? str($key)->replace('-', ' ')->title()->toString(),
            'route_uri' => $defaults['route_uri'] ?? null,
            'is_active' => true,
            'seo_title' => $defaults['seo']['title'] ?? null,
            'seo_description' => $defaults['seo']['description'] ?? null,
            'hero_eyebrow' => $defaults['hero']['eyebrow'] ?? null,
            'hero_title' => $defaults['hero']['title'] ?? null,
            'hero_description' => $defaults['hero']['description'] ?? null,
            'hero_image_url' => $defaults['hero']['imageUrl'] ?? null,
            'primary_cta_label' => $defaults['primaryCta']['label'] ?? null,
            'primary_cta_url' => $defaults['primaryCta']['url'] ?? null,
            'secondary_cta_label' => $defaults['secondaryCta']['label'] ?? null,
            'secondary_cta_url' => $defaults['secondaryCta']['url'] ?? null,
            'sidebar_label' => $defaults['sidebar']['label'] ?? null,
            'sidebar_title' => $defaults['sidebar']['title'] ?? null,
            'sidebar_body' => $defaults['sidebar']['body'] ?? null,
            'sidebar_items' => $defaults['sidebar']['items'] ?? null,
            'metrics' => $defaults['metrics'] ?? null,
            'content_sections' => $defaults['sections'] ?? null,
            'faqs' => $defaults['faqs'] ?? null,
        ];
    }

    public static function defaultPageDefinitions(): array
    {
        return [
            'about' => [
                'admin_title' => 'About',
                'route_uri' => '/about',
                'seo' => [
                    'title' => 'Travel Planning Agency in Dubai | About Acute Tourism',
                    'description' => 'Learn about Acute Tourism, a Dubai travel planning agency helping customers book tours, holiday packages, visa assistance, and curated travel experiences with human support.',
                ],
                'hero' => [
                    'eyebrow' => 'About Acute Tourism',
                    'title' => 'About Acute Tourism',
                    'description' => 'Acute Tourism LLC is a Dubai-based travel planning agency helping customers with Dubai tours, holiday packages, international visa assistance, corporate events, and Panoramic Bus experiences.',
                ],
                'primaryCta' => ['label' => 'Speak with our team', 'url' => '/contact'],
                'secondaryCta' => ['label' => 'View holiday packages', 'url' => '/dubai-holiday-packages'],
                'sidebar' => [
                    'label' => 'Why customers choose us',
                    'title' => 'One Dubai team for the important travel decisions.',
                    'items' => [
                        'Real support before you book, not only automated product listings.',
                        'Clear guidance across tours, packages, transfers, activities, and visa documents.',
                        'Dubai-based coordination with office presence and direct WhatsApp assistance.',
                        'Secure payment support and confirmation handled by the Acute Tourism team.',
                    ],
                ],
                'metrics' => [
                    ['value' => '12+', 'label' => 'Years of Dubai travel experience'],
                    ['value' => '2,500+', 'label' => 'Travelers assisted across trips and visas'],
                    ['value' => 'Licensed', 'label' => 'Dubai operator, license details available on request'],
                    ['value' => '2 hrs', 'label' => 'Typical WhatsApp response time'],
                ],
            ],
            'contact' => [
                'admin_title' => 'Contact',
                'route_uri' => '/contact',
                'seo' => [
                    'title' => 'Contact Acute Tourism | Travel Planning Support in Dubai',
                    'description' => 'Contact Acute Tourism for Dubai tours, holiday packages, panoramic bus experiences, outbound visa assistance, corporate travel, and custom travel planning support.',
                ],
                'hero' => [
                    'eyebrow' => 'Contact Acute Tourism',
                    'title' => 'Contact Acute Tourism',
                    'description' => 'Contact us for a new travel enquiry or support with an existing booking. Our team can assist with tours, holiday packages, visa assistance, Panoramic Bus, corporate events, and general travel planning support.',
                ],
                'sidebar' => [
                    'label' => 'Contact details',
                    'title' => 'Reach us directly',
                    'body' => 'For faster support, include your booking reference if you are an existing customer, or your travel date and service needed if you are making a new enquiry.',
                ],
                'sections' => [
                    [
                        'eyebrow' => 'Enquiry form',
                        'title' => 'Send your enquiry or support request.',
                        'body' => 'Share your trip timing, guest count, booking reference if available, and the type of support you want.',
                    ],
                ],
            ],
            'corporate-events' => [
                'admin_title' => 'Corporate Events',
                'route_uri' => '/corporate-travel-event-planning-dubai',
                'seo' => [
                    'title' => 'Corporate Travel and Event Planning Dubai | Acute Tourism',
                    'description' => 'Plan corporate travel and event experiences in Dubai with Acute Tourism, including group tours, transfers, team activities, event travel support, and dedicated coordination.',
                ],
                'hero' => [
                    'eyebrow' => 'Corporate events in Dubai',
                    'title' => 'Corporate Travel and Event Planning Dubai',
                    'description' => 'Acute Tourism helps companies, executives, HR teams, travel managers, and event organizers plan Dubai corporate experiences with prompt response, clear scope, human support, and reliable event-day coordination.',
                ],
                'primaryCta' => ['label' => 'Request a corporate proposal', 'url' => '/contact'],
                'secondaryCta' => ['label' => 'Speak to a consultant', 'url' => 'https://wa.me/971521926984?text=Hi%20Acute%20Tourism%2C%20I%20would%20like%20to%20plan%20a%20corporate%20event%20in%20Dubai.'],
                'sidebar' => [
                    'label' => 'Service promise',
                    'title' => 'Premium service, without slow back-and-forth.',
                    'items' => [
                        'Human consultation before pricing or proposal recommendations.',
                        'Prompt response through WhatsApp, phone, or email.',
                        'Clear event scope, inclusions, timing, and responsibilities.',
                        'Coordination for transport, activities, guest flow, and suppliers.',
                    ],
                ],
            ],
            'cancellation-policy' => [
                'admin_title' => 'Cancellation Policy',
                'route_uri' => '/cancellation-policy',
                'seo' => [
                    'title' => 'Cancellation Policy | Acute Tourism',
                    'description' => "Review Acute Tourism's cancellation policy for tours, tickets, holiday packages, visa assistance, panoramic bus experiences, and selected travel bookings.",
                ],
                'hero' => [
                    'eyebrow' => 'Acute Tourism Policy',
                    'title' => 'Cancellation Policy',
                    'description' => 'This page explains how cancellations, amendments, no-shows, and refunds are handled for tours, entry tickets, holiday packages, and other travel services arranged through Acute Tourism.',
                ],
                'primaryCta' => ['label' => 'Contact Acute Tourism', 'url' => '/contact'],
                'secondaryCta' => ['label' => 'View FAQ', 'url' => '/faq'],
                'sidebar' => [
                    'label' => 'Before you cancel',
                    'items' => [
                        'Have your booking reference ready',
                        'Check the service date and supplier terms',
                        'Note that package components may carry separate rules',
                        'Wait for written confirmation before assuming a cancellation is complete',
                    ],
                ],
            ],
            'terms-and-conditions' => [
                'admin_title' => 'Terms and Conditions',
                'route_uri' => '/terms-and-conditions',
                'seo' => [
                    'title' => 'Terms and Conditions | Acute Tourism',
                    'description' => 'Read the terms and conditions for using Acute Tourism services, including tours, packages, visa assistance, payments, cancellations, and bookings.',
                ],
                'hero' => [
                    'eyebrow' => 'Acute Tourism Policy',
                    'title' => 'Terms and Conditions',
                    'description' => 'These Terms & Conditions explain the general rules for using the Acute Tourism website, making enquiries, confirming bookings, paying for services, requesting changes, and using travel-related services arranged by Acute Tourism.',
                ],
                'primaryCta' => ['label' => 'Contact Acute Tourism', 'url' => '/contact'],
                'secondaryCta' => ['label' => 'Cancellation Policy', 'url' => '/cancellation-policy'],
                'sidebar' => [
                    'label' => 'Important',
                    'items' => [
                        'Availability is not final until confirmed',
                        'Supplier terms may affect the booking outcome',
                        'Customer-submitted details must be accurate',
                        'Policy updates may be made as operations evolve',
                    ],
                ],
            ],
            'privacy-policy' => [
                'admin_title' => 'Privacy Policy',
                'route_uri' => '/privacy-policy',
                'seo' => [
                    'title' => 'Privacy Policy | Acute Tourism',
                    'description' => 'Learn how Acute Tourism collects, uses, and protects customer information for travel bookings, inquiries, payments, and support services.',
                ],
                'hero' => [
                    'eyebrow' => 'Acute Tourism Policy',
                    'title' => 'Privacy Policy',
                    'description' => 'This Privacy Policy explains how Acute Tourism may collect, use, share, protect, and retain customer information when providing tours, holiday packages, visa assistance, corporate events, and related travel services.',
                ],
                'primaryCta' => ['label' => 'Contact Acute Tourism', 'url' => '/contact'],
                'secondaryCta' => ['label' => 'Terms & Conditions', 'url' => '/terms-and-conditions'],
                'sidebar' => [
                    'label' => 'At a glance',
                    'items' => [
                        'Inquiry and booking details may be stored for service handling',
                        'Relevant information may be shared with suppliers when needed',
                        'Payment and booking data may be retained for operational records',
                        'Policy updates may happen as systems and requirements evolve',
                    ],
                ],
            ],
            'faq' => [
                'admin_title' => 'FAQ',
                'route_uri' => '/faq',
                'seo' => [
                    'title' => 'FAQs | Acute Tourism',
                    'description' => 'Find answers to common questions about Acute Tourism tours, tickets, holiday packages, visa assistance, Panoramic Bus, corporate events, bookings, payments, cancellations, and refunds.',
                ],
                'hero' => [
                    'eyebrow' => 'Acute Tourism FAQs',
                    'title' => 'Frequently Asked Questions',
                    'description' => 'Find clear answers about Acute Tourism services, including tours and tickets, holiday packages, international visa assistance, Panoramic Bus, corporate events, bookings, payments, cancellations, and customer support.',
                ],
                'sidebar' => [
                    'label' => 'What this covers',
                    'items' => [
                        'Tours, tickets, and private bookings',
                        'Holiday packages and visa assistance',
                        'Panoramic Bus and corporate events',
                        'Payments, cancellations, and refunds',
                    ],
                ],
            ],
        ];
    }
}
