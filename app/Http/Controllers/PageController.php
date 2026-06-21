<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\BlogCategory;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Package;
use App\Models\SiteSetting;
use App\Models\Tour;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function home(): Response
    {
        $settings = SiteSetting::current();

        $featuredCollections = Collection::query()
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->get(['name', 'slug', 'summary', 'hero_image_path'])
            ->map(fn (Collection $collection) => [
                'name' => $collection->name,
                'slug' => $collection->slug,
                'summary' => $collection->summary,
                'heroImageUrl' => $collection->hero_image_url,
            ]);

        $featuredExperiences = Experience::query()
            ->where('is_active', true)
            ->where('show_on_homepage', true)
            ->orderBy('homepage_sort_order')
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(16)
            ->get(['title', 'slug', 'category', 'price_from', 'currency', 'tag', 'hero_image_path', 'short_description', 'duration', 'location'])
            ->map(fn (Experience $experience) => [
                'title' => $experience->title,
                'slug' => $experience->slug,
                'category' => $experience->category,
                'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                'tag' => $experience->tag,
                'summary' => $experience->short_description,
                'duration' => $experience->duration,
                'location' => $experience->location,
                'heroImageUrl' => $experience->hero_image_url,
            ]);

        if ($featuredExperiences->isEmpty()) {
            $featuredExperiences = Experience::query()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->limit(16)
                ->get(['title', 'slug', 'category', 'price_from', 'currency', 'tag', 'hero_image_path', 'short_description', 'duration', 'location'])
                ->map(fn (Experience $experience) => [
                    'title' => $experience->title,
                    'slug' => $experience->slug,
                    'category' => $experience->category,
                    'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                    'tag' => $experience->tag,
                    'summary' => $experience->short_description,
                    'duration' => $experience->duration,
                    'location' => $experience->location,
                    'heroImageUrl' => $experience->hero_image_url,
                ]);
        }

        $packages = Package::query()
            ->where('is_active', true)
            ->where('is_featured', true)
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->limit(3)
            ->get()
            ->values();

        if ($packages->isEmpty()) {
            $packages = Package::query()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('title')
                ->limit(3)
                ->get()
                ->values();
        }

        $packages = $packages->map(fn (Package $package, int $index) => [
            'title' => $package->title,
            'slug' => $package->slug,
            'summary' => $package->short_description,
            'duration' => $package->duration,
            'location' => $package->location,
            'priceFrom' => $this->formatMoney($package->price_from, $package->currency),
            'heroImageUrl' => $this->packageShowcaseImage($package, $index),
            'isFeatured' => $package->is_featured,
        ]);

        $packageCategories = $packages->map(fn (array $package) => [
            'title' => $package['title'],
            'summary' => $package['summary'],
            'priceLine' => $package['priceFrom'] ? "From {$package['priceFrom']} / person" : 'Custom quote',
            'detail' => $package['duration'] ?: 'Duration: Flexible',
            'href' => route('packages.show', $package['slug']),
            'cta' => 'Book Now',
            'image' => $package['heroImageUrl'],
            'highlights' => collect([
                $package['isFeatured'] ? 'Featured' : null,
                $package['location'],
                $package['duration'],
            ])->filter()->values()->all(),
        ]);

        $heroGallery = $featuredExperiences
            ->filter(fn (array $experience) => filled($experience['heroImageUrl']))
            ->take(6)
            ->map(fn (array $experience) => [
                'title' => $experience['title'],
                'slug' => $experience['slug'],
                'image' => $experience['heroImageUrl'],
                'tag' => $experience['tag'] ?: $experience['category'],
            ])
            ->values();

        $visaServices = collect([
            [
                'title' => 'Schengen Visa',
                'slug' => 'schengen-visa',
                'summary' => 'Dedicated consultation, documentation guidance, and embassy-facing preparation for Europe travel.',
                'tag' => 'Most Requested',
                'href' => route('visa.schengen'),
            ],
            [
                'title' => 'UK Visa',
                'slug' => 'uk-visa',
                'summary' => 'Tourist and short-stay guidance for UAE-based applicants heading to the United Kingdom.',
                'tag' => 'Popular',
                'href' => route('visa.uk'),
            ],
            [
                'title' => 'USA Visa',
                'slug' => 'usa-visa',
                'summary' => 'Profile review and application support focused on B1/B2 visitor intent and interview readiness.',
                'tag' => 'High Intent',
                'href' => route('visa.usa'),
            ],
            [
                'title' => 'Japan Visa',
                'slug' => 'japan-visa',
                'summary' => 'Travel plan and documentation support for short-term Japan tourist applications.',
                'tag' => 'Tourist',
                'href' => route('visa.japan'),
            ],
            [
                'title' => 'Canada Visa',
                'slug' => 'canada-visa',
                'summary' => 'Visitor visa assistance with strong document preparation and expectation setting.',
                'tag' => 'Visitor',
                'href' => route('visa.canada'),
            ],
            [
                'title' => 'Australia Visa',
                'slug' => 'australia-visa',
                'summary' => 'UAE residents: visitor visas with GTE-aware documentation and realistic timelines.',
                'tag' => 'Pacific',
                'href' => route('visa.australia'),
            ],
            [
                'title' => 'Malaysia Visa',
                'slug' => 'malaysia-visa',
                'summary' => 'Tourist and eVisa routes from the UAE—eligibility, checklist, and submission support.',
                'tag' => 'Southeast Asia',
                'href' => route('visa.malaysia'),
            ],
            [
                'title' => 'Vietnam Visa',
                'slug' => 'vietnam-visa',
                'summary' => 'eVisa and tourist visas with itinerary-aligned documents and clear application steps.',
                'tag' => 'Southeast Asia',
                'href' => route('visa.vietnam'),
            ],
            [
                'title' => 'E-Visa Assistance',
                'slug' => 'evisa-assistance',
                'summary' => 'Fast-turn support for online visa workflows where document clarity and submission accuracy matter.',
                'tag' => 'Fast Track',
                'href' => route('visa.evisa'),
            ],
        ]);

        $mustDoExperiences = $featuredExperiences->take(6);
        $topRatedExperiences = $featuredExperiences->slice(6)->values();

        return Inertia::render('Home', [
            'seo' => [
                'title' => 'Custom Travel Planning in Dubai | Acute Tourism',
                'description' => 'Plan better trips with Acute Tourism - custom travel planning in Dubai, tours and tickets, holiday packages, outbound visa assistance, and expert human support.',
            ],
            'hero' => [
                'eyebrow' => 'Custom Travel Planning in Dubai',
                'title' => 'Travel Planned Around You',
                'mobileTitle' => 'Tours, Packages & Visa Assistance',
                'description' => 'Book Dubai experiences, plan holidays, arrange panoramic bus travel, and get outbound visa assistance with expert human support.',
                'heroImageUrl' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=2000&q=80',
                'videoUrl' => 'https://acutetourism.org/videos/hero-section-intro.mp4',
                'primaryCta' => ['label' => $settings->home_primary_cta_label ?: 'Explore Dubai Experiences', 'href' => route('experiences.index')],
                'secondaryCta' => ['label' => $settings->home_secondary_cta_label ?: 'View Packages', 'href' => route('packages.index')],
                'tertiaryCta' => ['label' => 'Visa Services', 'href' => route('visa.index')],
                'trustLine' => '2,500+ travelers | 12 years in Dubai | Licensed operator',
            ],
            'homeSections' => [
                'ribbonEyebrow' => 'In Dubai & beyond',
                'ribbonTitle' => 'Curated Travel Moments',
                'collectionsEyebrow' => $settings->home_collections_eyebrow ?: 'Destinations',
                'collectionsTitle' => $settings->home_collections_title ?: 'Our Core Travel Services',
                'mustDoEyebrow' => 'Start here',
                'mustDoTitle' => "Dubai's Must-Do Experiences",
                'topRatedEyebrow' => 'Guest favourites',
                'topRatedTitle' => 'Top-Rated Dubai Experiences',
                'packagesEyebrow' => 'Holiday planning',
                'packagesTitle' => 'Our Top Holiday Packages',
                'recommendationsEyebrow' => 'Global routes',
                'recommendationsTitle' => 'Visa services & international travel',
                'companyEyebrow' => 'Why travel with us',
                'companyTitle' => 'We are the Best Company for your Visit',
                'companyCopy' => 'From five-star desert camps to Schengen paperwork, one team handles your itinerary, documents, and on-ground support, so you spend less time coordinating and more time enjoying the trip.',
                'testimonialsEyebrow' => 'Reviews',
                'testimonialsTitle' => 'What our travelers say',
            ],
            'collections' => [
                [
                    'name' => 'Visa Services',
                    'slug' => 'visa-services',
                    'summary' => 'Schengen, UK, USA, Japan, Canada, Brazil, South Africa, e-visa, and tourist visa assistance.',
                    'href' => route('visa.index'),
                ],
                [
                    'name' => 'International Tour Packages',
                    'slug' => 'packages',
                    'summary' => 'Premium package-led itineraries designed for higher-intent planning and cleaner decision making.',
                    'href' => route('packages.index'),
                ],
                [
                    'name' => 'Luxury Experiences in Dubai',
                    'slug' => 'experiences',
                    'summary' => 'Private desert, yacht, city, and concierge-led experiences presented with stronger visual polish.',
                    'href' => route('experiences.index'),
                ],
            ],
            'featuredExperiences' => $featuredExperiences,
            'mustDoExperiences' => $mustDoExperiences,
            'topRatedExperiences' => $topRatedExperiences,
            'heroGallery' => $heroGallery,
            'packages' => $packages,
            'packageCategories' => $packageCategories,
            'recommendations' => $visaServices,
            'serviceFocus' => [
                [
                    'title' => 'Dubai Experiences',
                    'copy' => 'Book top Dubai tours and tickets.',
                    'href' => route('experiences.index'),
                    'cta' => 'Explore now',
                    'tag' => 'Tours & Tickets',
                ],
                [
                    'title' => 'Holiday Packages',
                    'copy' => 'Plan complete Dubai holidays with support.',
                    'href' => route('packages.index'),
                    'cta' => 'View packages',
                    'tag' => 'Custom Trips',
                ],
                [
                    'title' => 'Visa Services',
                    'copy' => 'Prepare outbound visa documents.',
                    'href' => route('visa.index'),
                    'cta' => 'Check visas',
                    'tag' => 'Visa Guidance',
                ],
                [
                    'title' => 'Panoramic Bus',
                    'copy' => 'Exclusive UAE day trips with panoramic views.',
                    'href' => route('bus-tour'),
                    'cta' => 'View Panoramic Bus',
                    'tag' => 'Exclusive',
                ],
            ],
            'testimonials' => [],
            'trustProof' => [
                'reviewSource' => $settings->google_reviews_url ? 'Google' : 'Tripadvisor',
                'reviewsUrl' => $settings->google_reviews_url ?: $settings->tripadvisor_reviews_url,
                'licenseText' => $settings->license_number
                    ? "DTCM / DED license {$settings->license_number}"
                    : 'Dubai licensed operator - license number available on request',
                'paymentText' => 'Secure payment via Network payment gateway. Booking confirmation and team follow-up are handled by email and WhatsApp.',
                'partnerProof' => $settings->partner_proof ?? [],
            ],
        ]);
    }

    public function acuteLanding(): Response
    {
        $settings = SiteSetting::current();

        $featuredPackages = Package::query()
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->limit(3)
            ->get()
            ->values()
            ->map(fn (Package $package, int $index) => [
                'title' => $package->title,
                'summary' => $package->short_description,
                'duration' => $package->duration,
                'priceFrom' => $this->formatMoney($package->price_from, $package->currency),
                'heroImageUrl' => $this->packageShowcaseImage($package, $index),
                'galleryImageUrls' => $this->packageGalleryShowcaseImages($package),
                'href' => route('packages.index'),
                'highlights' => collect([
                    $package->location,
                    $package->duration,
                    $package->price_from ? 'Premium itinerary' : null,
                ])->filter()->values()->all(),
            ]);

        return Inertia::render('AcuteLanding', [
            'seo' => [
                'title' => 'Acute Landing',
                'description' => 'A standalone premium landing page for Acute Tourism based on the approved visual direction.',
            ],
            'contact' => [
                'email' => $settings->contact_email,
                'phone' => $settings->contact_phone,
                'phoneSecondary' => $settings->contact_phone_secondary,
                'address' => $settings->contact_address,
                'whatsappNumber' => $settings->whatsapp_number,
            ],
            'hero' => [
                'eyebrow' => 'Premium Travel Experience',
                'title' => 'Luxury travel and global visa services under one polished Acute brand.',
                'description' => 'A dedicated one-page landing experience for high-intent travelers who want premium packages, visa guidance, and fast contact without searching through the full website.',
                'backgroundImage' => 'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1800&q=80',
                'stats' => [
                    ['value' => '4.9', 'label' => 'Client rating'],
                    ['value' => Experience::query()->where('is_active', true)->count().'+', 'label' => 'Curated experiences'],
                    ['value' => '24/7', 'label' => 'WhatsApp access'],
                ],
            ],
            'serviceCategories' => [
                [
                    'title' => 'Visa Services',
                    'copy' => 'Schengen, UK, USA, Canada, Japan, Brazil, South Africa, and e-visa support with clearer guidance and faster lead handling.',
                    'image' => 'https://images.unsplash.com/photo-1521295121783-8a321d551ad2?auto=format&fit=crop&w=1200&q=80',
                    'href' => route('visa.index'),
                ],
                [
                    'title' => 'International Tour Packages',
                    'copy' => 'Curated premium itineraries built for travelers who want a more selective offer than a crowded package catalog.',
                    'image' => 'https://images.unsplash.com/photo-1500530855697-b586d89ba3ee?auto=format&fit=crop&w=1200&q=80',
                    'href' => route('packages.index'),
                ],
                [
                    'title' => 'Luxury Experiences in Dubai',
                    'copy' => 'Private desert, yacht, city, and concierge-led experiences presented with the same premium visual language as the landing page.',
                    'image' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=80',
                    'href' => route('experiences.index'),
                ],
            ],
            'featuredPackages' => $featuredPackages,
            'visaCategories' => [
                ['emoji' => 'EU', 'title' => 'Schengen Visa', 'copy' => '27 European countries', 'href' => route('visa.schengen')],
                ['emoji' => 'UK', 'title' => 'UK Visa', 'copy' => 'United Kingdom support', 'href' => route('visa.uk')],
                ['emoji' => 'US', 'title' => 'USA Visa', 'copy' => 'Visitor and profile review', 'href' => route('visa.usa')],
                ['emoji' => 'JP', 'title' => 'Japan Visa', 'copy' => 'Short-stay planning', 'href' => route('visa.japan')],
                ['emoji' => 'CA', 'title' => 'Canada Visa', 'copy' => 'Visitor visa assistance', 'href' => route('visa.canada')],
                ['emoji' => 'BR', 'title' => 'Brazil Visa', 'copy' => 'Travel document support', 'href' => route('visa.brazil')],
                ['emoji' => 'ZA', 'title' => 'South Africa Visa', 'copy' => 'Tourist application guidance', 'href' => route('visa.south-africa')],
                ['emoji' => 'EV', 'title' => 'E-Visa Assistance', 'copy' => 'Online processing routes', 'href' => route('visa.evisa')],
            ],
            'processSteps' => [
                ['step' => '01', 'title' => 'Call or WhatsApp Acute', 'copy' => 'Reach the team directly using the numbers on the page instead of filling a long multi-step funnel.'],
                ['step' => '02', 'title' => 'Get the right route', 'copy' => 'We guide the lead into the right service path: visa support, package planning, or Dubai experiences.'],
                ['step' => '03', 'title' => 'Review and confirm', 'copy' => 'Documents, travel preferences, and service details are aligned before the next booking step starts.'],
                ['step' => '04', 'title' => 'Move fast', 'copy' => 'The landing page keeps decisions simple so high-intent visitors can reach Acute without friction.'],
            ],
            'testimonials' => [
                [
                    'quote' => 'The presentation feels premium, but the strongest part was how quickly we reached the team and got clear direction.',
                    'name' => 'Corporate traveler',
                    'service' => 'Visa consultation',
                ],
                [
                    'quote' => 'We moved from inquiry to package discussion without bouncing around the website. That made the process feel much more serious.',
                    'name' => 'Family planner',
                    'service' => 'International package',
                ],
                [
                    'quote' => 'The page looks polished and the contact options are obvious. That is exactly what a conversion page should do.',
                    'name' => 'Repeat client',
                    'service' => 'Dubai experience',
                ],
            ],
            'faqItems' => [
                [
                    'question' => 'Is this page separate from the main website?',
                    'answer' => 'Yes. This is a dedicated one-page landing page so campaign traffic can convert faster without navigating the full site structure first.',
                ],
                [
                    'question' => 'Can I contact Acute directly from this page?',
                    'answer' => 'Yes. The page uses the live Acute phone number and WhatsApp number so visitors can call or message immediately.',
                ],
                [
                    'question' => 'Does Acute handle both travel and visa services?',
                    'answer' => 'Yes. The landing page is built around both premium travel planning and professional visa support under one brand.',
                ],
                [
                    'question' => 'Can this page send visitors to the full website?',
                    'answer' => 'Yes. Each major section can still route the user into the relevant service pages when more detail is needed.',
                ],
            ],
        ]);
    }

    public function busTour(): Response
    {
        return Inertia::render('BusTour', [
            'seo' => [
                'title' => 'Luxury Bus Tour Dubai | Panoramic UAE Bus Trips',
                'description' => "Join Acute Tourism's luxury bus tour Dubai experience with panoramic views, exclusive seats, curated UAE day trips, hotel pick-up, sightseeing, meals, and guided support.",
            ],
        ]);
    }

    public function partnerWithUs(): Response
    {
        return Inertia::render('TourgratPartner', [
            'seo' => [
                'title' => 'Travel Referral Program Dubai | Earn With Tourgrat',
                'description' => "Earn with Tourgrat by sharing travel leads, tracking bookings, and receiving rewards through Acute Tourism's referral program for tours, packages, and travel services.",
            ],
        ]);
    }

    public function experiences(Request $request): Response
    {
        return $this->renderExperiencesIndex();
    }

    public function experienceLocation(string $location): Response
    {
        abort_unless(array_key_exists($location, $this->experienceLocationFilters()), 404);

        return $this->renderExperiencesIndex(locationFilter: $location);
    }

    public function experienceCategory(string $category): Response
    {
        abort_unless(array_key_exists($category, $this->experienceTypeFilters()), 404);

        return $this->renderExperiencesIndex(typeFilter: $category);
    }

    public function experience(string $slug): Response
    {
        $experience = Experience::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'collections:id,name,slug',
                'reviews' => fn ($query) => $query->published()->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('reviewed_at'),
            ])
            ->firstOrFail();

        $relatedExperiences = Experience::query()
            ->where('is_active', true)
            ->where('id', '!=', $experience->id)
            ->where(function ($query) use ($experience) {
                $query->where('category', $experience->category)
                    ->orWhereHas('collections', function ($collectionQuery) use ($experience) {
                        $collectionQuery->whereIn('collections.id', $experience->collections->pluck('id'));
                    });
            })
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->limit(3)
            ->get()
            ->map(fn (Experience $item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'category' => $item->category,
                'duration' => $item->duration,
                'priceFrom' => $this->formatMoney($item->price_from, $item->currency),
                'heroImageUrl' => $item->hero_image_url,
            ]);

        return Inertia::render('Experiences/Show', [
            'seo' => [
                'title' => $experience->seo_title ?: $experience->title,
                'description' => $experience->seo_description ?: $experience->short_description,
                'image' => $experience->hero_image_url,
            ],
            'experience' => [
                'title' => $experience->title,
                'slug' => $experience->slug,
                'category' => $experience->category,
                'tag' => $experience->tag,
                'shortDescription' => $experience->short_description,
                'heroSummary' => $experience->hero_summary,
                'description' => $experience->description,
                'heroImageUrl' => $experience->hero_image_url,
                'heroVideoUrl' => $experience->hero_video_url,
                'galleryImageUrls' => $experience->gallery_image_urls,
                'galleryVideoUrls' => $experience->gallery_video_urls,
                'mediaItems' => $this->mediaItems(
                    $experience->hero_video_url,
                    $experience->hero_image_url,
                    $experience->gallery_video_urls,
                    $experience->gallery_image_urls,
                ),
                'duration' => $experience->duration,
                'location' => $experience->location,
                'pickupNote' => $experience->pickup_note,
                'experienceType' => $experience->experience_type,
                'transferOption' => $experience->transfer_option,
                'bookingType' => $experience->booking_type,
                'bookingOptions' => $this->pricedBookingOptions($experience->booking_options, $experience->currency),
                'availability' => $this->availabilityPayload($experience),
                'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                'priceFromValue' => $experience->price_from ? (float) $experience->price_from : null,
                'childPriceFrom' => $this->formatMoney($experience->child_price_from, $experience->currency),
                'childPriceFromValue' => $experience->child_price_from ? (float) $experience->child_price_from : null,
                'isPrivate' => $experience->is_private,
                'highlights' => $experience->highlights ?? [],
                'inclusions' => $experience->inclusions ?? [],
                'exclusions' => $experience->exclusions ?? [],
                'importantNotices' => $experience->important_notices ?? [],
                'expectationSteps' => $experience->expectation_steps ?? [],
                'bestFor' => $experience->best_for ?? [],
                'faqs' => $experience->faqs ?? [],
                'cancellationPolicy' => $experience->cancellation_policy,
                'collections' => $experience->collections->map(fn (Collection $collection) => [
                    'name' => $collection->name,
                    'slug' => $collection->slug,
                ]),
                'averageRating' => $this->averageRating($experience->reviews),
                'reviewCount' => $experience->reviews->count(),
                'reviews' => $this->presentReviews($experience->reviews),
            ],
            'inquiryDefaults' => [
                'interest' => $this->interestForCategory($experience->category),
                'message' => "I would like details and availability for {$experience->title}.",
            ],
            'relatedExperiences' => $relatedExperiences,
        ]);
    }

    public function collection(string $slug): Response
    {
        $collection = Collection::query()
            ->where('slug', $slug)
            ->with([
                'experiences' => fn ($query) => $query->where('is_active', true)->orderBy('collection_experience.sort_order'),
                'tours' => fn ($query) => $query->where('is_active', true)->orderBy('collection_tour.sort_order'),
            ])
            ->firstOrFail();

        $experiences = $collection->experiences->map(fn (Experience $experience) => [
            'title' => $experience->title,
            'slug' => $experience->slug,
            'category' => $experience->category,
            'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
            'duration' => $experience->duration,
            'heroImageUrl' => $experience->hero_image_url,
            'href' => route('experiences.show', $experience->slug),
            'label' => 'View experience',
        ]);

        $tours = $collection->tours->map(fn (Tour $tour) => [
            'title' => $tour->title,
            'slug' => $tour->slug,
            'category' => $tour->category,
            'priceFrom' => $this->formatMoney($tour->price_from, $tour->currency),
            'duration' => $tour->duration,
            'heroImageUrl' => $tour->hero_image_url,
            'href' => route('tours.show', $tour->slug),
            'label' => 'View tour',
        ]);

        return Inertia::render('Collections/Show', [
            'seo' => [
                'title' => $collection->seo_title ?: $collection->name,
                'description' => $collection->seo_description ?: $collection->summary,
                'image' => $collection->hero_image_url,
            ],
            'collection' => [
                'slug' => $collection->slug,
                'title' => $collection->name,
                'description' => $collection->description,
                'summary' => $collection->summary,
                'heroImageUrl' => $collection->hero_image_url,
                'experiences' => $experiences->concat($tours)->values(),
            ],
        ]);
    }

    public function packages(): Response
    {
        $packages = Package::query()
            ->where('is_active', true)
            ->with(['collections' => fn ($query) => $query->where('collection_group', 'package')->orderBy('collections.sort_order')->orderBy('collections.name')])
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->get()
            ->values();

        $packageFilters = Collection::query()
            ->where('collection_group', 'package')
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get(['name', 'slug'])
            ->map(fn (Collection $collection) => [
                'key' => $collection->slug,
                'label' => $collection->name,
            ])
            ->values();

        return Inertia::render('Packages/Index', [
            'seo' => [
                'title' => 'Dubai Holiday Packages with Travel Support | Acute Tourism',
                'description' => 'Plan Dubai holiday packages with Acute Tourism, including hotels, transfers, tours, attraction tickets, desert safaris, and travel support for families, couples, and groups.',
            ],
            'packages' => $packages
                ->map(fn (Package $package, int $index) => [
                    'title' => $package->title,
                    'slug' => $package->slug,
                    'summary' => $package->short_description,
                    'duration' => $package->duration,
                    'location' => $package->location,
                    'priceFrom' => $package->price_from ? "{$package->currency} ".number_format((float) $package->price_from, 0) : null,
                    'heroImageUrl' => $this->packageShowcaseImage($package, $index),
                    'categories' => $package->collections
                        ->map(fn (Collection $collection) => [
                            'name' => $collection->name,
                            'slug' => $collection->slug,
                        ])
                        ->values(),
                ]),
            'packageFilters' => $packageFilters,
        ]);
    }

    public function tours(): Response
    {
        $tours = Tour::query()
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->get()
            ->values();

        return Inertia::render('Tours/Index', [
            'seo' => [
                'title' => 'Tours',
                'description' => 'Guided city, culture, and private tour products with direct booking from the detail page.',
            ],
            'tours' => $tours->map(fn (Tour $tour) => [
                'title' => $tour->title,
                'slug' => $tour->slug,
                'category' => $tour->category,
                'summary' => $tour->short_description,
                'duration' => $tour->duration,
                'location' => $tour->location,
                'priceFrom' => $this->formatMoney($tour->price_from, $tour->currency),
                'heroImageUrl' => $tour->hero_image_url,
            ]),
        ]);
    }

    public function package(string $slug): Response
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'reviews' => fn ($query) => $query->published()->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('reviewed_at'),
            ])
            ->firstOrFail();

        $relatedPackages = Package::query()
            ->where('is_active', true)
            ->where('id', '!=', $package->id)
            ->orderByDesc('is_featured')
            ->limit(3)
            ->get()
            ->values()
            ->map(fn (Package $item, int $index) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'summary' => $item->short_description,
                'duration' => $item->duration,
                'heroImageUrl' => $this->packageShowcaseImage($item, $index),
            ]);

        return Inertia::render('Packages/Show', [
            'seo' => [
                'title' => $package->seo_title ?: $package->title,
                'description' => $package->seo_description ?: $package->short_description,
                'image' => $this->packageShowcaseImage($package, 0),
            ],
            'packageItem' => [
                'title' => $package->title,
                'slug' => $package->slug,
                'shortDescription' => $package->short_description,
                'description' => $package->description,
                'heroImageUrl' => $this->packageShowcaseImage($package, 0),
                'heroVideoUrl' => $package->hero_video_url,
                'galleryImageUrls' => $this->packageGalleryShowcaseImages($package),
                'galleryVideoUrls' => $package->gallery_video_urls,
                'mediaItems' => $this->mediaItems(
                    $package->hero_video_url,
                    $this->packageShowcaseImage($package, 0),
                    $package->gallery_video_urls,
                    $this->packageGalleryShowcaseImages($package),
                ),
                'duration' => $package->duration,
                'days' => $package->days,
                'nights' => $package->nights,
                'location' => $package->location,
                'groupSize' => $package->group_size_min && $package->group_size_max
                    ? "{$package->group_size_min} - {$package->group_size_max}"
                    : null,
                'priceFrom' => $package->price_from ? "{$package->currency} ".number_format((float) $package->price_from, 0) : null,
                'salePrice' => $package->sale_price ? "{$package->currency} ".number_format((float) $package->sale_price, 0) : null,
                'highlights' => $package->highlights ?? [],
                'inclusions' => $package->inclusions ?? [],
                'exclusions' => $package->exclusions ?? [],
                'importantNotices' => $package->important_notices ?? [],
                'bestFor' => $package->best_for ?? [],
                'cancellationPolicy' => $package->cancellation_policy,
                'itinerary' => $package->itinerary ?? [],
                'averageRating' => $this->averageRating($package->reviews),
                'reviewCount' => $package->reviews->count(),
                'reviews' => $this->presentReviews($package->reviews),
            ],
            'relatedPackages' => $relatedPackages,
        ]);
    }

    public function tour(string $slug): Response
    {
        $tour = Tour::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->with([
                'reviews' => fn ($query) => $query->published()->orderByDesc('is_featured')->orderBy('sort_order')->orderByDesc('reviewed_at'),
            ])
            ->firstOrFail();

        $relatedTours = Tour::query()
            ->where('is_active', true)
            ->where('id', '!=', $tour->id)
            ->orderByDesc('is_featured')
            ->limit(3)
            ->get()
            ->values()
            ->map(fn (Tour $item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'category' => $item->category,
                'duration' => $item->duration,
                'priceFrom' => $this->formatMoney($item->price_from, $item->currency),
                'heroImageUrl' => $item->hero_image_url,
            ]);

        return Inertia::render('Tours/Show', [
            'seo' => [
                'title' => $tour->seo_title ?: $tour->title,
                'description' => $tour->seo_description ?: $tour->short_description,
                'image' => $tour->hero_image_url,
            ],
            'tour' => [
                'title' => $tour->title,
                'slug' => $tour->slug,
                'category' => $tour->category,
                'shortDescription' => $tour->short_description,
                'description' => $tour->description,
                'heroImageUrl' => $tour->hero_image_url,
                'heroVideoUrl' => $tour->hero_video_url,
                'galleryImageUrls' => $tour->gallery_image_urls,
                'galleryVideoUrls' => $tour->gallery_video_urls,
                'mediaItems' => $this->mediaItems(
                    $tour->hero_video_url,
                    $tour->hero_image_url,
                    $tour->gallery_video_urls,
                    $tour->gallery_image_urls,
                ),
                'duration' => $tour->duration,
                'location' => $tour->location,
                'pickupNote' => $tour->pickup_note,
                'experienceType' => $tour->experience_type,
                'transferOption' => $tour->transfer_option,
                'bookingType' => $tour->booking_type,
                'bookingOptions' => $this->pricedBookingOptions($tour->booking_options, $tour->currency),
                'availability' => $this->availabilityPayload($tour),
                'priceFrom' => $this->formatMoney($tour->price_from, $tour->currency),
                'priceFromValue' => $tour->price_from ? (float) $tour->price_from : null,
                'childPriceFrom' => $this->formatMoney($tour->child_price_from, $tour->currency),
                'childPriceFromValue' => $tour->child_price_from ? (float) $tour->child_price_from : null,
                'isPrivate' => $tour->is_private,
                'highlights' => $tour->highlights ?? [],
                'inclusions' => $tour->inclusions ?? [],
                'exclusions' => $tour->exclusions ?? [],
                'importantNotices' => $tour->important_notices ?? [],
                'expectationSteps' => $tour->expectation_steps ?? [],
                'bestFor' => $tour->best_for ?? [],
                'faqs' => $tour->faqs ?? [],
                'cancellationPolicy' => $tour->cancellation_policy,
                'averageRating' => $this->averageRating($tour->reviews),
                'reviewCount' => $tour->reviews->count(),
                'reviews' => $this->presentReviews($tour->reviews),
            ],
            'relatedTours' => $relatedTours,
        ]);
    }

    public function schengenVisa(): Response
    {
        return $this->renderVisaProduct('schengen-visa');

        $settings = SiteSetting::current();

        return Inertia::render('VisaLanding', [
            'seo' => [
                'title' => 'Schengen Visa Dubai | Acute Tourism',
                'description' => 'Travel with confidence: Schengen visa help from Dubai with document review, application guidance, appointment planning, and practical travel support.',
            ],
            'contact' => [
                'email' => $settings->contact_email,
                'phone' => $settings->contact_phone,
                'phoneSecondary' => $settings->contact_phone_secondary,
                'address' => $settings->contact_address,
                'whatsappNumber' => $settings->whatsapp_number,
            ],
            'interestOptions' => $settings->interest_options ?? [],
            'hero' => [
                'eyebrow' => 'Visa support & travel planning',
                'title' => 'Travel with confidence',
                'description' => 'Tourist visas, document guidance, and destination planning for travelers who want a smoother path from application to departure, starting with Schengen from Dubai.',
                'highlights' => [
                    'Schengen-focused consultation for UAE residents',
                    'Document checks and realistic timelines',
                    'Appointment planning and follow-up in one flow',
                    'Direct support with clear next steps when timing matters',
                ],
                'stats' => [
                    ['value' => '27+', 'label' => 'Schengen states'],
                    ['value' => 'Fast', 'label' => 'Lead response'],
                    ['value' => 'Guided', 'label' => 'File prep'],
                ],
            ],
            'anchorLinks' => [
                ['label' => 'Services', 'href' => '#offer'],
                ['label' => 'Routes', 'href' => '#routes'],
                ['label' => 'Process', 'href' => '#process'],
                ['label' => 'Countries', 'href' => '#countries'],
                ['label' => 'Documents', 'href' => '#documents'],
                ['label' => 'FAQ', 'href' => '#faq'],
                ['label' => 'Contact', 'href' => '#contact-us'],
            ],
            'serviceCategories' => [
                [
                    'eyebrow' => 'Itineraries',
                    'title' => 'Travel planning',
                    'copy' => 'Customized holiday planning across Europe and beyond with practical support from departure to return.',
                    'href' => url('/dubai-holiday-packages'),
                    'cta' => 'View packages',
                ],
                [
                    'eyebrow' => 'Approvals',
                    'title' => 'Visa consultation',
                    'copy' => 'End-to-end support for Schengen and other routes with document checks and application guidance.',
                    'href' => url('/tourist-visa-assistance-uae-residents'),
                    'cta' => 'All visa services',
                ],
                [
                    'eyebrow' => 'Experiences',
                    'title' => 'Dubai experiences',
                    'copy' => 'Tours, desert safaris, and itinerary planning for travelers visiting the UAE.',
                    'href' => url('/dubai-tours-and-tickets'),
                    'cta' => 'Browse experiences',
                ],
            ],
            'expertBullets' => [
                'Document review',
                'Application guidance',
                'Submission tracking',
                'Travel readiness support',
            ],
            'visaRoutes' => [
                ['code' => 'EU', 'title' => 'Schengen Visa', 'subtitle' => '27 European countries', 'href' => '#visa-form', 'featured' => true],
                ['code' => 'UK', 'title' => 'UK Visa', 'subtitle' => 'United Kingdom', 'href' => url('/tourist-visa-assistance-uae-residents#uk-visa')],
                ['code' => 'US', 'title' => 'USA Visa', 'subtitle' => 'United States', 'href' => url('/tourist-visa-assistance-uae-residents#usa-visa')],
                ['code' => 'JP', 'title' => 'Japan Visa', 'subtitle' => 'Japan', 'href' => url('/tourist-visa-assistance-uae-residents#japan-visa')],
                ['code' => 'CA', 'title' => 'Canada Visa', 'subtitle' => 'Canada', 'href' => url('/tourist-visa-assistance-uae-residents#canada-visa')],
                ['code' => 'ZA', 'title' => 'South Africa Visa', 'subtitle' => 'South Africa', 'href' => url('/tourist-visa-assistance-uae-residents#south-africa-visa')],
                ['code' => 'AU', 'title' => 'Australia Visa', 'subtitle' => 'Australia', 'href' => url('/tourist-visa-assistance-uae-residents#australia-visa')],
                ['code' => 'MY', 'title' => 'Malaysia Visa', 'subtitle' => 'Malaysia', 'href' => url('/tourist-visa-assistance-uae-residents#malaysia-visa')],
                ['code' => 'VN', 'title' => 'Vietnam Visa', 'subtitle' => 'Vietnam', 'href' => url('/tourist-visa-assistance-uae-residents#vietnam-visa')],
                ['code' => 'TR', 'title' => 'Turkey Visa', 'subtitle' => 'Turkey', 'href' => url('/tourist-visa-assistance-uae-residents')],
                ['code' => 'EV', 'title' => 'Other eVisa help', 'subtitle' => 'Online routes', 'href' => url('/tourist-visa-assistance-uae-residents#evisa-assistance')],
            ],
            'visualGallery' => [
                [
                    'title' => 'Europe city break',
                    'image' => 'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=1600&q=80',
                ],
                [
                    'title' => 'Premium travel planning',
                    'image' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1600&q=80',
                ],
                [
                    'title' => 'Travel concierge',
                    'image' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1600&q=80',
                ],
                [
                    'title' => 'European itinerary',
                    'image' => 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=1600&q=80',
                ],
            ],
            'urgencyCards' => [
                [
                    'eyebrow' => 'Fast track',
                    'title' => 'Appointment in 24-48 hrs',
                    'copy' => 'When your country, purpose, and travel month are already clear, we prioritize coordination.',
                    'badge' => 'Limited slots',
                    'image' => 'https://images.unsplash.com/photo-1548013146-72479768bada?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'eyebrow' => 'Priority',
                    'title' => 'Within 7 days',
                    'copy' => 'Active planning with documents still in progress, and we align dates with your file readiness.',
                    'badge' => 'Quick coordination',
                    'image' => 'https://images.unsplash.com/photo-1500375592092-40eb2168fd21?auto=format&fit=crop&w=1200&q=80',
                ],
                [
                    'eyebrow' => 'Planned',
                    'title' => '15-30 days',
                    'copy' => 'Best when you want a stronger file before booking, not only the earliest slot.',
                    'badge' => 'Advised route',
                    'image' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=80',
                ],
            ],
            'eligibleCountries' => [
                'Austria',
                'Belgium',
                'Bulgaria',
                'Croatia',
                'Czech Republic',
                'Denmark',
                'Estonia',
                'Finland',
                'France',
                'Germany',
                'Greece',
                'Hungary',
                'Iceland',
                'Italy',
                'Latvia',
                'Lithuania',
                'Luxembourg',
                'Malta',
                'Netherlands',
                'Norway',
                'Poland',
                'Portugal',
                'Romania',
                'Slovakia',
                'Slovenia',
                'Spain',
                'Sweden',
                'Switzerland',
            ],
            'processSteps' => [
                [
                    'title' => 'Free consultation',
                    'copy' => 'Share your travel goals and we assess route, timeline, and documentation before anything is filed.',
                ],
                [
                    'title' => 'Planning & documents',
                    'copy' => 'We organize paperwork, bookings, and supporting material with a clear checklist.',
                ],
                [
                    'title' => 'Submission & tracking',
                    'copy' => 'Your case moves forward with updates on progress, pending actions, and realistic timing.',
                ],
                [
                    'title' => 'Travel ready',
                    'copy' => 'After approval, we help you move from application status to a travel-ready plan.',
                ],
            ],
            'documentGroups' => [
                [
                    'title' => 'Core documents',
                    'items' => [
                        'Valid passport with at least 6 months validity',
                        'UAE residence visa or Emirates ID with valid status',
                        'Recent passport-size photographs',
                        'Completed Schengen visa application form',
                    ],
                ],
                [
                    'title' => 'Travel support',
                    'items' => [
                        'Flight reservation or travel itinerary',
                        'Hotel booking or invitation letter where applicable',
                        'Travel insurance with minimum 30,000 EUR coverage',
                        'Travel plan aligned with the first-entry or main-stay country',
                    ],
                ],
                [
                    'title' => 'Financial and professional proof',
                    'items' => [
                        'Recent bank statements',
                        'Salary certificate or company trade documents',
                        'NOC, leave approval, or business ownership proof',
                        'Any previous visas or travel history that supports the file',
                    ],
                ],
            ],
            'documentHighlights' => [
                ['title' => 'Valid Passport', 'copy' => 'Sufficient validity and blank pages for processing'],
                ['title' => 'UAE Residence', 'copy' => 'Valid residence status and Emirates ID support'],
                ['title' => 'Reservations', 'copy' => 'Flight and hotel booking or consistent itinerary proof'],
                ['title' => 'Travel Insurance', 'copy' => 'Coverage aligned with Schengen requirements'],
                ['title' => 'Financial Proof', 'copy' => 'Bank statements, salary support, or business documents'],
            ],
            'testimonials' => [
                [
                    'name' => 'Emma L.',
                    'role' => 'UAE visit visa',
                    'quote' => 'Our documents were reviewed properly, the process was clear, and the team stayed responsive until approval. Everything felt organized and reliable.',
                ],
                [
                    'name' => 'Luca M.',
                    'role' => 'Schengen visa',
                    'quote' => 'The Schengen application was handled with real attention to detail. We avoided mistakes, submitted on time, and got clear guidance throughout.',
                ],
                [
                    'name' => 'Aisha K.',
                    'role' => 'Travel package',
                    'quote' => 'We used them for visa support and trip planning. Flights, hotel advice, and local bookings were coordinated well, which made the trip much easier.',
                ],
            ],
            'faqItems' => [
                [
                    'question' => 'What destinations do you cover?',
                    'answer' => 'We assist with Schengen, UK, USA, Canada, Japan, Australia, Malaysia, Vietnam, Turkey, South Africa, and many eVisa routes. This page focuses on Schengen; see Visa Services for the full list.',
                ],
                [
                    'question' => 'How long does the visa process take?',
                    'answer' => 'Processing times vary by embassy and season. Schengen visas often take roughly 5-15 business days once submitted, and we set expectations early while tracking pending steps.',
                ],
                [
                    'question' => 'What is a Schengen visa?',
                    'answer' => 'A Schengen visa lets eligible travelers visit participating European countries on one visa, subject to type, validity, and entry rules.',
                ],
                [
                    'question' => 'What documents are usually required?',
                    'answer' => 'Typically passport, UAE residence proof, application form, travel reservations, insurance, and financial or employment documents. See the checklist on this page.',
                ],
                [
                    'question' => 'Can you guarantee approval?',
                    'answer' => 'No. Embassies decide outcomes. We provide document guidance, realistic planning, and follow-up so your file is as strong and complete as possible.',
                ],
            ],
            'contactPoints' => [
                ['label' => 'Company phone', 'value' => $settings->contact_phone],
                [
                    'label' => 'Additional phone',
                    'value' => $settings->contact_phone_secondary
                        ? $settings->contact_phone.' / '.$settings->contact_phone_secondary
                        : $settings->contact_phone,
                ],
                ['label' => 'Company email', 'value' => $settings->contact_email],
                ['label' => 'Office address', 'value' => $settings->contact_address],
            ],
        ]);
    }

    public function canadaVisa(): Response
    {
        return $this->renderVisaProduct('canada-visa');
    }

    public function visaProduct(string $slug): Response
    {
        return $this->renderVisaProduct($slug);
    }

    private function renderVisaProduct(string $slug): Response
    {
        $settings = SiteSetting::current();
        $visa = $this->visaProductPages()[$slug] ?? abort(404);

        return Inertia::render('VisaProduct', [
            'seo' => [
                'title' => $visa['seoTitle'],
                'description' => $visa['seoDescription'],
            ],
            'contact' => [
                'email' => $settings->contact_email,
                'phone' => $settings->contact_phone,
                'phoneSecondary' => $settings->contact_phone_secondary,
                'address' => $settings->contact_address,
                'whatsappNumber' => $settings->whatsapp_number,
            ],
            'interestOptions' => $settings->interest_options ?? [],
            'visa' => $visa,
        ]);
    }

    /**
     * Customer-facing visa product page content. This keeps all visa detail pages
     * driven by one reusable Vue template until an admin CRUD is needed.
     *
     * @return array<string, array<string, mixed>>
     */
    private function visaProductPages(): array
    {
        return collect([
            'schengen-visa' => [
                'shortTitle' => 'Schengen Visa',
                'kicker' => 'Schengen Visa Assistance',
                'title' => 'Prepare Your Schengen Visa File With Clear Guidance',
                'copy' => 'Understand the Schengen tourist visa route, required documents, insurance, travel itinerary, and appointment expectations before an Acute Tourism consultant reviews your file.',
                'image' => 'https://images.unsplash.com/photo-1499856871958-5b9627545d1a?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Europe Visa',
                'processing' => '15-30',
                'fee' => 'AED 350',
                'type' => 'Sticker visa',
                'style' => 'Embassy appointment',
                'mainDocs' => 'Passport, bank statement, insurance, itinerary',
                'bestFor' => 'Europe tourism and short stays',
                'authority' => 'issuing Schengen embassy or consulate',
            ],
            'uk-visa' => [
                'shortTitle' => 'UK Visa',
                'kicker' => 'UK Visa Assistance',
                'title' => 'Prepare Your UK Visitor Visa With Clear Document Guidance',
                'copy' => 'Understand the UK visitor visa route, required evidence, and common weak points before an Acute Tourism consultant reviews your file and next steps.',
                'image' => 'https://images.unsplash.com/photo-1513635269975-59663e0ac1ad?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Standard Visitor',
                'processing' => '8-15',
                'fee' => 'AED 300',
                'type' => 'Standard Visitor Visa',
                'style' => 'Online + VFS biometrics',
                'mainDocs' => 'Passport, funds, travel proof',
                'bestFor' => 'Tourism, family visit, short stay',
                'authority' => 'UK Visas and Immigration',
            ],
            'usa-visa' => [
                'shortTitle' => 'USA Visa',
                'kicker' => 'USA Visa Assistance',
                'title' => 'Get B1/B2 Visitor Visa Guidance Before Your Interview',
                'copy' => 'Review the DS-160 route, interview expectations, travel purpose, and supporting profile before you move ahead with your USA visitor visa application.',
                'image' => 'https://images.unsplash.com/photo-1501594907352-04cda38ebc29?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'B1/B2 Visitor',
                'processing' => '30-90',
                'fee' => 'AED 450',
                'type' => 'B1/B2 Visitor Visa',
                'style' => 'DS-160 + embassy interview',
                'mainDocs' => 'Passport, DS-160, profile, funds',
                'bestFor' => 'Tourism, business visit, family visit',
                'authority' => 'United States embassy or consulate',
            ],
            'canada-visa' => [
                'shortTitle' => 'Canada Visa',
                'kicker' => 'Canada Visa Assistance',
                'title' => 'Get Canada Visitor Visa Guidance Without the Confusion',
                'copy' => 'Get enough clarity online to understand the Canada visitor visa route, then speak with an Acute Tourism visa consultant to review your profile, documents, and next steps before you proceed.',
                'image' => 'https://images.unsplash.com/photo-1517935706615-2717063c2225?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Visitor Visa',
                'processing' => '60-120',
                'fee' => 'AED 400',
                'type' => 'Temporary Resident Visa',
                'style' => 'Online + biometrics',
                'mainDocs' => 'Passport, funds, travel purpose',
                'bestFor' => 'Tourism, family visit, short stay',
                'authority' => 'Canadian immigration authority',
            ],
            'japan-visa' => [
                'shortTitle' => 'Japan Visa',
                'kicker' => 'Japan Visa Assistance',
                'title' => 'Prepare a Cleaner Japan Tourist Visa File',
                'copy' => 'Get guidance on itinerary structure, financial proof, UAE residence documents, and embassy expectations before submitting a Japan short-stay visa file.',
                'image' => 'https://images.unsplash.com/photo-1540959733332-eab4deabeeaf?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Short-stay Visa',
                'processing' => '5-10',
                'fee' => 'AED 280',
                'type' => 'Short-stay Tourist Visa',
                'style' => 'Embassy submission',
                'mainDocs' => 'Passport, itinerary, bank proof',
                'bestFor' => 'Tourism, family visit, short stay',
                'authority' => 'Japanese embassy or consulate',
            ],
            'australia-visa' => [
                'shortTitle' => 'Australia Visa',
                'kicker' => 'Australia Visa Assistance',
                'title' => 'Get Australia Visitor Visa Support With Stronger File Clarity',
                'copy' => 'Prepare your visitor visa route with clearer travel purpose, financial proof, employment evidence, and realistic timeline expectations.',
                'image' => 'https://images.unsplash.com/photo-1523482580672-f109ba8cb9be?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Visitor Visa',
                'processing' => '20-40',
                'fee' => 'AED 380',
                'type' => 'Visitor Visa',
                'style' => 'Online application',
                'mainDocs' => 'Passport, funds, travel purpose',
                'bestFor' => 'Tourism, family visit, short stay',
                'authority' => 'Australian immigration authority',
            ],
            'turkey-visa' => [
                'shortTitle' => 'Turkey eVisa',
                'kicker' => 'Turkey eVisa Assistance',
                'title' => 'Check Turkey eVisa Eligibility and Apply With Clear Details',
                'copy' => 'Confirm the right online route, document details, and travel information before your Turkey eVisa application is prepared.',
                'image' => 'https://images.unsplash.com/photo-1527838832700-5059252407fa?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Fast eVisa',
                'processing' => '24-72',
                'fee' => 'AED 150',
                'type' => 'Turkey eVisa',
                'style' => 'Online application',
                'mainDocs' => 'Passport, photo, travel details',
                'bestFor' => 'Tourism and short stays',
                'authority' => 'Turkish immigration authority',
            ],
            'malaysia-visa' => [
                'shortTitle' => 'Malaysia Visa',
                'kicker' => 'Malaysia Visa Assistance',
                'title' => 'Prepare Your Malaysia Tourist or eVisa Application Clearly',
                'copy' => 'Check eligibility, documents, and online submission details before moving ahead with a Malaysia tourist visa or eVisa route.',
                'image' => 'https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Tourist / eVisa',
                'processing' => '3-5',
                'fee' => 'AED 120',
                'type' => 'Tourist / eVisa',
                'style' => 'Online application',
                'mainDocs' => 'Passport, photo, travel details',
                'bestFor' => 'Tourism and short stays',
                'authority' => 'Malaysian immigration authority',
            ],
            'vietnam-visa' => [
                'shortTitle' => 'Vietnam Visa',
                'kicker' => 'Vietnam Visa Assistance',
                'title' => 'Prepare Your Vietnam Tourist or eVisa Route Clearly',
                'copy' => 'Get help checking the right Vietnam visa route, travel information, passport details, and online submission steps before you proceed.',
                'image' => 'https://images.unsplash.com/photo-1528127269322-539801943592?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Tourist / eVisa',
                'processing' => '3-7',
                'fee' => 'AED 150',
                'type' => 'Tourist / eVisa',
                'style' => 'Online application',
                'mainDocs' => 'Passport, photo, travel details',
                'bestFor' => 'Tourism and short stays',
                'authority' => 'Vietnamese immigration authority',
            ],
            'brazil-visa' => [
                'shortTitle' => 'Brazil Visa',
                'kicker' => 'Brazil Visa Assistance',
                'title' => 'Get Brazil Visa Document Guidance Before You Apply',
                'copy' => 'Prepare a cleaner Brazil tourist visa file with clearer travel purpose, financial proof, accommodation details, and supporting documents.',
                'image' => 'https://images.unsplash.com/photo-1483729558449-99ef09a8c325?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Tourist Visa',
                'processing' => '10-20',
                'fee' => 'AED 300',
                'type' => 'Tourist visa',
                'style' => 'Consular / online route',
                'mainDocs' => 'Passport, funds, itinerary, accommodation',
                'bestFor' => 'Tourism and family visits',
                'authority' => 'Brazilian consulate or immigration authority',
            ],
            'south-africa-visa' => [
                'shortTitle' => 'South Africa Visa',
                'kicker' => 'South Africa Visa Assistance',
                'title' => 'Prepare Your South Africa Tourist Visa With Better Clarity',
                'copy' => 'Get practical document guidance for South Africa travel, including passport, funds, itinerary, accommodation, and travel-purpose support.',
                'image' => 'https://images.unsplash.com/photo-1484318571209-661cf29a69fa?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Tourist Visa',
                'processing' => '10-20',
                'fee' => 'AED 300',
                'type' => 'Tourist visa',
                'style' => 'Consular submission',
                'mainDocs' => 'Passport, funds, itinerary, accommodation',
                'bestFor' => 'Tourism and short stays',
                'authority' => 'South African consulate or immigration authority',
            ],
            'evisa-assistance' => [
                'shortTitle' => 'eVisa Assistance',
                'kicker' => 'Online Visa Assistance',
                'title' => 'Get Help With Online Visa Applications and eVisa Routes',
                'copy' => 'Use Acute Tourism for clearer data entry, document preparation, and route selection when the destination offers an online visa process.',
                'image' => 'https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'Online Visa',
                'processing' => 'Varies',
                'fee' => 'On request',
                'type' => 'eVisa / Online Visa',
                'style' => 'Online submission',
                'mainDocs' => 'Passport, photo, travel details',
                'bestFor' => 'Online visa destinations',
                'authority' => 'destination immigration authority',
            ],
            'tourist-visa-assistance' => [
                'shortTitle' => 'Tourist Visa Assistance',
                'kicker' => 'Tourist Visa Assistance',
                'title' => 'Find the Right Tourist Visa Route Before You Apply',
                'copy' => 'If you are unsure which visa route applies, Acute Tourism can review your destination, nationality, UAE residence status, timing, and document readiness.',
                'image' => 'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=1000&q=80',
                'badge' => 'General Visa Help',
                'processing' => 'Varies',
                'fee' => 'On request',
                'type' => 'Tourist visa support',
                'style' => 'Route depends on destination',
                'mainDocs' => 'Passport, funds, travel plan',
                'bestFor' => 'Travelers unsure of the right route',
                'authority' => 'destination embassy, consulate, or immigration authority',
            ],
        ])->map(fn (array $page, string $slug) => $this->makeVisaProductPage($slug, $page))->all();
    }

    /**
     * @param  array<string, mixed>  $page
     * @return array<string, mixed>
     */
    private function makeVisaProductPage(string $slug, array $page): array
    {
        $shortTitle = $page['shortTitle'];
        $authority = $page['authority'];
        $processing = $page['processing'];
        $fee = $page['fee'];

        return $page + [
            'slug' => $slug,
            'seoTitle' => "{$shortTitle} Assistance | Acute Tourism",
            'seoDescription' => "{$shortTitle} assistance for UAE residents with document guidance, file review, processing timeline clarity, and application preparation support.",
            'requirementsTitle' => "{$shortTitle} Requirements",
            'requirementsCopy' => 'Use this as a starting checklist. Exact requirements depend on nationality, UAE residence status, employment type, travel history, and purpose of visit.',
            'feesCopy' => "{$shortTitle} processing can vary by applicant profile, season, appointment availability, and authority review time.",
            'processTitle' => "How {$shortTitle} Support Works",
            'processCopy' => 'The page gives you the basic information. The real value is the direct consultation where an Acute visa agent reviews your situation and guides you personally.',
            'whyTitle' => 'Information Online. Final Clarity With a Real Agent.',
            'whyCopy' => "{$shortTitle} applications can feel complicated because the strength of the file depends on your personal situation, not just a general checklist.",
            'disclaimer' => "{$shortTitle} approval is decided only by the {$authority}. Acute Tourism provides document guidance, application preparation support, and file readiness review, but does not guarantee approval or processing time.",
            'faqTitle' => "{$shortTitle} FAQs",
            'faqCopy' => "Quick answers to help UAE residents understand the {$shortTitle} support process.",
            'stats' => [
                ['4.8★', 'Customer rating'],
                [$processing, 'Working days estimate'],
                [$fee, 'Service fee from'],
            ],
            'quickFacts' => [
                ['Visa type', $page['type']],
                ['Application style', $page['style']],
                ['Main documents', $page['mainDocs']],
                ['Best for', $page['bestFor']],
            ],
            'feeCards' => [
                ['Acute Service Fee', $fee, ['Document checklist guidance', 'Profile-based document review', 'Application preparation support', 'Clear next-step guidance'], true],
                ['Processing Estimate', $processing, ['Working days estimate', 'Timeline may vary', 'Appointment or biometrics may be required', 'Authority decision only'], false],
                ['Application Style', $page['style'], ['Route-specific preparation', 'Document consistency review', 'Submission guidance', 'Follow-up steps explained'], false],
            ],
            'reviewCards' => [
                ["The team helped me understand what was missing in my {$shortTitle} documents before I applied.", 'Ahmed R.'],
                ['Clear guidance and fast response. The document checklist made the process easier to manage.', 'Mariam A.'],
                ['I liked that they explained the weak areas in my file instead of just asking for documents.', 'Priya S.'],
            ],
            'faqItems' => [
                ["Can Acute Tourism guarantee {$shortTitle} approval?", "No. {$shortTitle} approval is decided only by the {$authority}. Acute Tourism helps with document guidance, file preparation, and readiness review."],
                ["How long does {$shortTitle} processing take?", "The estimate shown here is {$processing} working days where applicable. Actual timelines can vary based on applicant profile, appointments, season, and authority review."],
                ['What documents are most important?', 'Passport, UAE residence proof, bank statement, employment or business proof, travel purpose, and supporting documents are commonly important. Exact requirements vary.'],
                ['Can you help if I started the application already?', 'Yes. Acute can help review your file readiness and identify missing or weak documents, depending on your application stage.'],
                ['What happens after I send the form?', 'A visa consultant reviews your details and contacts you with a clearer checklist, route, and next step.'],
            ],
        ];
    }

    public function visaServices(): Response
    {
        $settings = SiteSetting::current();

        return Inertia::render('VisaServices', [
            'seo' => [
                'title' => 'Tourist Visa Assistance for UAE Residents | Acute Tourism',
                'description' => 'Get tourist visa assistance for UAE residents, including document guidance for Schengen, UK, USA, Japan, Canada, Australia, and selected e-visa destinations.',
            ],
            'contact' => [
                'email' => $settings->contact_email,
                'phone' => $settings->contact_phone,
                'phoneSecondary' => $settings->contact_phone_secondary,
                'address' => $settings->contact_address,
                'whatsappNumber' => $settings->whatsapp_number,
            ],
            'hero' => [
                'eyebrow' => 'Visa Services',
                'title' => 'Tourist Visa Assistance for UAE Residents',
                'description' => 'From Schengen and major western destinations to Australia, Malaysia, and Vietnam, with clear categories and a dedicated Schengen page for full guidance.',
            ],
            'visaCategories' => [
                [
                    'id' => 'schengen-visa',
                    'title' => 'Schengen Visa',
                    'tag' => 'Dedicated Landing Page',
                    'copy' => 'A separate landing page designed for campaign traffic, document guidance, and high-intent consultation requests.',
                    'href' => route('visa.schengen'),
                    'cta' => 'Open Schengen page',
                ],
                [
                    'id' => 'uk-visa',
                    'title' => 'UK Visa',
                    'tag' => 'Tourist',
                    'copy' => 'Short-stay UK travel support with attention to documentation quality, profile clarity, and timeline planning.',
                    'href' => route('visa.uk'),
                    'cta' => 'Open UK page',
                ],
                [
                    'id' => 'usa-visa',
                    'title' => 'USA Visa',
                    'tag' => 'Interview',
                    'copy' => 'B1/B2 visitor-focused guidance for profile review, supporting file preparation, and expectation setting.',
                    'href' => route('visa.usa'),
                    'cta' => 'Open USA page',
                ],
                [
                    'id' => 'japan-visa',
                    'title' => 'Japan Visa',
                    'tag' => 'Travel Planning',
                    'copy' => 'Short-stay Japan visitor visa support with a stronger focus on itinerary structure and documentation consistency.',
                    'href' => route('visa.japan'),
                    'cta' => 'Open Japan page',
                ],
                [
                    'id' => 'canada-visa',
                    'title' => 'Canada Visa',
                    'tag' => 'Visitor',
                    'copy' => 'Tourist and family-visit support with a cleaner narrative around financial proof, residence status, and travel purpose.',
                    'href' => route('visa.canada'),
                    'cta' => 'Open Canada page',
                ],
                [
                    'id' => 'evisa-assistance',
                    'title' => 'E-visa Assistance',
                    'tag' => 'Online Processing',
                    'copy' => 'Online application support for destinations where submission speed and data accuracy matter.',
                    'href' => route('visa.evisa'),
                    'cta' => 'Open eVisa page',
                ],
                [
                    'id' => 'brazil-visa',
                    'title' => 'Brazil Visa',
                    'tag' => 'Tourist',
                    'copy' => 'Application planning and supporting-document guidance for travelers heading to Brazil.',
                    'href' => route('visa.brazil'),
                    'cta' => 'Open Brazil page',
                ],
                [
                    'id' => 'south-africa-visa',
                    'title' => 'South Africa Visa',
                    'tag' => 'Tourist',
                    'copy' => 'Visa support for South Africa itineraries with practical document preparation and travel-purpose framing.',
                    'href' => route('visa.south-africa'),
                    'cta' => 'Open South Africa page',
                ],
                [
                    'id' => 'australia-visa',
                    'title' => 'Australia Visa',
                    'tag' => 'Visitor',
                    'copy' => 'Visitor and short-stay Australia visas for UAE residents with documentation support, Genuine Temporary Entrant (GTE) framing, and realistic embassy timelines.',
                    'href' => route('visa.australia'),
                    'cta' => 'Open Australia page',
                ],
                [
                    'id' => 'turkey-visa',
                    'title' => 'Turkey eVisa',
                    'tag' => 'eVisa',
                    'copy' => 'Fast online visa assistance based on eligibility and travel plan.',
                    'href' => route('visa.turkey'),
                    'cta' => 'Open Turkey page',
                ],
                [
                    'id' => 'malaysia-visa',
                    'title' => 'Malaysia Visa',
                    'tag' => 'Tourist / eVisa',
                    'copy' => 'Malaysia tourist and eVisa routes from the UAE: eligibility checks, checklist-led preparation, and submission support.',
                    'href' => route('visa.malaysia'),
                    'cta' => 'Open Malaysia page',
                ],
                [
                    'id' => 'vietnam-visa',
                    'title' => 'Vietnam Visa',
                    'tag' => 'Tourist / eVisa',
                    'copy' => 'Vietnam eVisa and tourist routes with itinerary-aligned documents and clear steps from application to approval.',
                    'href' => route('visa.vietnam'),
                    'cta' => 'Open Vietnam page',
                ],
                [
                    'id' => 'tourist-visa-assistance',
                    'title' => 'Tourist Visa Assistance',
                    'tag' => 'General Support',
                    'copy' => 'A broader consultation route for travelers who are unsure which visa process applies to their trip.',
                    'href' => route('visa.tourist'),
                    'cta' => 'Open Tourist Visa page',
                ],
            ],
            'servicePoints' => [
                'Direct contact for quick questions and consultation booking',
                'Structured preparation: checklists, timelines, and consistent documentation',
                'Dedicated Schengen page for step-by-step guidance and high-intent requests',
                'One overview of visa types so you can pick the right route before you apply',
            ],
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'seo' => [
                'title' => 'Travel Planning Agency in Dubai | About Acute Tourism',
                'description' => 'Learn about Acute Tourism, a Dubai travel planning agency helping customers book tours, holiday packages, visa assistance, and curated travel experiences with human support.',
            ],
            'pillars' => [
                'Dubai-based team with local travel and supplier knowledge.',
                'Support across planning, booking, confirmation, and follow-up.',
                'Clear communication before payment or document submission.',
            ],
        ]);
    }

    public function cancellationPolicy(): Response
    {
        return Inertia::render('CancellationPolicy', [
            'seo' => [
                'title' => 'Cancellation Policy | Acute Tourism',
                'description' => "Review Acute Tourism's cancellation policy for tours, tickets, holiday packages, visa assistance, panoramic bus experiences, and selected travel bookings.",
            ],
            'policySections' => [
                [
                    'title' => 'General Booking Policy',
                    'items' => [
                        'Cancellation and refund terms may vary depending on the product, supplier, booking date, travel date, and what is included in the service.',
                        'Acute Tourism will share the applicable cancellation terms before or at the time of booking where supplier-specific conditions apply.',
                        'Customers should not assume a cancellation or refund is approved until written confirmation is received from Acute Tourism.',
                    ],
                ],
                [
                    'title' => 'Tours and Experiences',
                    'items' => [
                        'Generally, tours and experiences are refundable if cancelled at least 24 hours before the scheduled tour start time.',
                        'Cancellations made less than 24 hours before the tour start time may be non-refundable.',
                        'Failure to arrive at the pickup point, late arrival, or missed participation may be treated as a no-show and may be non-refundable.',
                        'Some tours may have stricter supplier terms, especially if they involve private arrangements, limited capacity, special permits, or third-party confirmation.',
                    ],
                ],
                [
                    'title' => 'Entry Tickets and Attraction Tickets',
                    'items' => [
                        'Entry tickets and attraction tickets are generally non-refundable once issued or confirmed.',
                        'This includes, but is not limited to, attraction tickets, theme park tickets, museum tickets, event tickets, show tickets, and time-slot based admissions.',
                        'Date changes for entry tickets depend on the supplier or attraction rules and are not guaranteed.',
                    ],
                ],
                [
                    'title' => 'Holiday Packages',
                    'items' => [
                        'Cancellation terms for holiday packages are dynamic and depend on the travel date, hotels, transfers, tickets, flights where applicable, supplier rules, and what is included in the package.',
                        'For every holiday package, the applicable cancellation policy will be shared at the time of booking or before final confirmation.',
                        'Some package components may be fully refundable, partially refundable, or non-refundable depending on supplier conditions.',
                        'If a holiday package includes hotel stays, special event access, entry tickets, transport, or third-party services, each component may follow its own cancellation rule.',
                        'Any change in supplier terms, availability, seasonality, or travel date may affect the final cancellation or refund amount.',
                    ],
                ],
                [
                    'title' => 'Date Changes and Amendments',
                    'items' => [
                        'Date changes and amendments are handled case by case.',
                        'Approval depends on supplier availability, price difference, original booking terms, and whether the change is requested within the allowed time.',
                        'Additional charges may apply for date changes, guest name changes, hotel changes, transport changes, or service upgrades.',
                        'A change request is not confirmed until Acute Tourism provides written confirmation.',
                    ],
                ],
                [
                    'title' => 'Refund Processing Timeline',
                    'items' => [
                        'Approved refunds are usually processed within 5 to 7 working days after the refund has been approved.',
                        "The final time for the amount to reflect in the customer's account may also depend on the bank, card issuer, or payment provider.",
                        'Acute Tourism can confirm the refund status from our side, but bank or payment gateway posting times are outside our control.',
                    ],
                ],
                [
                    'title' => 'Transaction Charges and Payment Provider Fees',
                    'items' => [
                        'Transaction charges, card fees, gateway fees, or payment provider charges paid during booking may not be refunded.',
                        'These charges are paid to external payment providers and may be deducted from the refundable amount where applicable.',
                        'Any refundable amount will be calculated after considering supplier charges, non-refundable components, and payment provider charges where applicable.',
                    ],
                ],
                [
                    'title' => 'How to Request a Cancellation or Refund',
                    'items' => [
                        'Contact Acute Tourism with your booking reference, lead traveler name, service date, and booked product or package.',
                        'The team will review the booking terms and supplier conditions before confirming the refund status.',
                        'Refunds, cancellations, and amendments should only be considered approved once confirmed in writing by Acute Tourism.',
                    ],
                ],
            ],
        ]);
    }

    public function termsAndConditions(): Response
    {
        return Inertia::render('TermsAndConditions', [
            'seo' => [
                'title' => 'Terms and Conditions | Acute Tourism',
                'description' => 'Read the terms and conditions for using Acute Tourism services, including tours, packages, visa assistance, payments, cancellations, and bookings.',
            ],
            'termsSections' => [
                [
                    'title' => 'Use of the Website',
                    'items' => [
                        'By using the Acute Tourism website, making an enquiry, submitting a form, or confirming a booking, you agree to these Terms and Conditions.',
                        'The website is intended to provide information about tours, holiday packages, visa assistance, corporate events, and related travel services.',
                        'Acute Tourism may update website content, pricing, availability, policies, and service details at any time where necessary.',
                    ],
                ],
                [
                    'title' => 'Bookings and Confirmations',
                    'items' => [
                        'A booking is only considered confirmed after payment or agreed deposit has been received and written confirmation has been issued by Acute Tourism.',
                        'Availability may change before confirmation, especially for hotels, private tours, attraction tickets, transport, seasonal products, and high-demand dates.',
                        'Customers are responsible for checking booking details, guest names, dates, pickup locations, inclusions, exclusions, and timing before confirming.',
                    ],
                ],
                [
                    'title' => 'Pricing and Payment',
                    'items' => [
                        'Prices may vary depending on season, supplier availability, group size, travel date, hotel category, service level, and exchange or supplier changes.',
                        'Any quote shared by Acute Tourism is valid only for the stated period or until availability changes.',
                        'Full payment or a deposit may be required depending on the service type.',
                        'Payment provider fees, transaction charges, bank charges, or card charges may apply and may not be refundable.',
                    ],
                ],
                [
                    'title' => 'Customer Responsibilities',
                    'items' => [
                        'Customers must provide accurate names, contact details, travel dates, passport information where needed, pickup details, and any other information required to arrange the service.',
                        'Customers must arrive on time for tours, transfers, pickups, tickets, appointments, and scheduled activities.',
                        'Customers are responsible for checking passport validity, visa requirements, travel restrictions, health requirements, insurance, and personal eligibility before travel.',
                        'Acute Tourism is not responsible for service disruption caused by incorrect information provided by the customer.',
                    ],
                ],
                [
                    'title' => 'Supplier and Third-Party Services',
                    'items' => [
                        'Some services are operated by third-party suppliers such as hotels, transport companies, attractions, theme parks, event venues, embassies, consulates, immigration authorities, and activity providers.',
                        'Supplier rules, operating hours, safety requirements, ticket conditions, cancellation rules, and availability may apply.',
                        'Acute Tourism will assist with coordination, but some final decisions or service conditions may depend on the relevant third-party provider.',
                    ],
                ],
                [
                    'title' => 'Visa Assistance Terms',
                    'items' => [
                        'Acute Tourism provides visa assistance, document guidance, appointment support where applicable, and application preparation support.',
                        'Visa approval is not guaranteed by Acute Tourism.',
                        'Final visa decisions are made only by the relevant embassy, consulate, immigration authority, or government department.',
                        'Visa fees, service fees, appointment fees, and document-related charges may be non-refundable once processing or submission has started.',
                    ],
                ],
                [
                    'title' => 'Changes, Cancellations, and Refunds',
                    'items' => [
                        'All changes, cancellations, and refunds are subject to the applicable cancellation and refund policy.',
                        'Tours are generally refundable if cancelled at least 24 hours before the scheduled tour start time, unless supplier-specific conditions state otherwise.',
                        'Entry tickets are generally non-refundable once issued or confirmed.',
                        'Holiday package cancellation rules are dynamic and depend on the travel date and package components.',
                        'Approved refunds are usually processed within 5 to 7 working days after approval.',
                    ],
                ],
                [
                    'title' => 'Liability and Force Majeure',
                    'items' => [
                        'Acute Tourism is not responsible for delays, cancellations, losses, or changes caused by events outside our reasonable control.',
                        'This may include weather conditions, traffic, accidents, government restrictions, supplier disruption, technical issues, force majeure events, or changes imposed by authorities.',
                        'Where possible, Acute Tourism will assist with alternatives or rescheduling, subject to supplier terms and availability.',
                    ],
                ],
                [
                    'title' => 'Governing Law',
                    'items' => [
                        'These Terms and Conditions are intended to be governed by the applicable laws and regulations of the United Arab Emirates.',
                        'Any dispute should first be raised with Acute Tourism directly so both parties can attempt to resolve the matter fairly.',
                        'If a matter cannot be resolved directly, it may be handled through the appropriate legal or regulatory channels in the UAE.',
                    ],
                ],
            ],
        ]);
    }

    public function privacyPolicy(): Response
    {
        return Inertia::render('PrivacyPolicy', [
            'seo' => [
                'title' => 'Privacy Policy | Acute Tourism',
                'description' => 'Learn how Acute Tourism collects, uses, and protects customer information for travel bookings, inquiries, payments, and support services.',
            ],
            'privacySections' => [
                [
                    'title' => 'Information We Collect',
                    'items' => [
                        'We may collect personal information such as name, phone number, email address, nationality, travel dates, destination, booking details, and enquiry details.',
                        'For visa assistance or travel-related services, we may collect passport details, UAE residence information, employment details, travel history, financial documents, hotel bookings, flight details, and other supporting documents where required.',
                        'We may also collect technical information such as website usage, device information, browser type, and interaction data for website improvement and security.',
                    ],
                ],
                [
                    'title' => 'How We Use Your Information',
                    'items' => [
                        'We use customer information to respond to enquiries, prepare quotes, arrange bookings, process payments, coordinate travel services, and provide customer support.',
                        'For visa assistance, information may be used to review document readiness, prepare applications, guide customers, or support appointment and submission processes where applicable.',
                        'We may use contact details to send booking updates, service reminders, payment information, policy updates, or relevant travel communication.',
                    ],
                ],
                [
                    'title' => 'Sharing Information with Service Providers',
                    'items' => [
                        'We may share necessary information with trusted third-party providers to complete a service requested by the customer.',
                        'These providers may include hotels, transport companies, attractions, tour operators, payment providers, visa centers, embassies, consulates, immigration-related service providers, insurance providers, or event partners.',
                        'Only the information required to deliver or support the requested service will be shared where reasonably possible.',
                    ],
                ],
                [
                    'title' => 'Payment and Transaction Information',
                    'items' => [
                        'Payments may be processed through secure third-party payment providers.',
                        'Acute Tourism does not intend to store full card details on the website unless handled through approved secure payment systems.',
                        'Payment providers may collect and process transaction information according to their own security and privacy standards.',
                    ],
                ],
                [
                    'title' => 'Document Handling',
                    'items' => [
                        'Travel and visa documents may contain sensitive personal information, so customers should only submit documents through approved communication channels shared by Acute Tourism.',
                        'Documents are used only for the purpose of providing the requested travel, visa, booking, or support service.',
                        'Customers should ensure that all documents submitted are accurate, valid, and lawfully provided.',
                    ],
                ],
                [
                    'title' => 'Data Security',
                    'items' => [
                        'Acute Tourism takes reasonable steps to protect customer information from unauthorized access, misuse, loss, or disclosure.',
                        'No online transmission or storage system can be guaranteed to be completely secure, but we aim to use appropriate safeguards and responsible handling practices.',
                        'Access to customer information is limited to team members or service providers who need it to support the requested service.',
                    ],
                ],
                [
                    'title' => 'Data Retention',
                    'items' => [
                        'Customer information may be retained for as long as needed to provide the service, manage booking records, comply with legal obligations, handle disputes, support accounting requirements, or improve customer service.',
                        'Visa and travel documents may be retained only as long as reasonably required for the service or related record purposes.',
                        'Customers may request deletion of certain information, subject to legal, operational, accounting, or dispute-resolution requirements.',
                    ],
                ],
                [
                    'title' => 'Marketing Communication',
                    'items' => [
                        'Acute Tourism may send service updates, travel offers, or relevant marketing communication where permitted.',
                        'Customers can request to stop receiving promotional communication by contacting Acute Tourism.',
                        'Important booking, payment, policy, or service-related messages may still be sent when necessary.',
                    ],
                ],
                [
                    'title' => 'Customer Rights and Contact',
                    'items' => [
                        'Customers may contact Acute Tourism to request access, correction, or deletion of personal information where applicable.',
                        'Requests will be reviewed based on legal, operational, and service-related requirements.',
                        'For privacy-related requests, contact Acute Tourism through the official email or phone number listed on this page.',
                    ],
                ],
            ],
        ]);
    }

    public function corporateEvents(): Response
    {
        return Inertia::render('CorporateEvents', [
            'seo' => [
                'title' => 'Corporate Travel and Event Planning Dubai | Acute Tourism',
                'description' => 'Plan corporate travel and event experiences in Dubai with Acute Tourism, including group tours, transfers, team activities, event travel support, and dedicated coordination.',
            ],
            'services' => [
                'Executive desert experiences',
                'Private yacht hosting',
                'Incentive itineraries',
                'VIP ground handling',
            ],
        ]);
    }

    public function contact(): Response
    {
        $settings = SiteSetting::current();

        return Inertia::render('Contact', [
            'seo' => [
                'title' => 'Contact Acute Tourism | Travel Planning Support in Dubai',
                'description' => 'Contact Acute Tourism for Dubai tours, holiday packages, panoramic bus experiences, outbound visa assistance, corporate travel, and custom travel planning support.',
            ],
            'contact' => [
                'email' => $settings->contact_email,
                'phone' => $settings->contact_phone,
                'phoneSecondary' => $settings->contact_phone_secondary,
                'address' => $settings->contact_address,
                'whatsappNumber' => $settings->whatsapp_number,
            ],
            'interestOptions' => ['Tours & Tickets', 'Holiday Package', 'International Visa Assistance', 'Panoramic Bus', 'Corporate Events', 'General Planning', 'Existing Booking Support'],
        ]);
    }

    public function journal(Request $request): Response
    {
        $selectedCategorySlug = $request->query('category');
        $selectedCategory = $selectedCategorySlug
            ? BlogCategory::query()->active()->where('slug', $selectedCategorySlug)->first()
            : null;

        $articleQuery = Article::query()
            ->with('blogCategory')
            ->published()
            ->when($selectedCategory, fn ($query) => $query->where('blog_category_id', $selectedCategory->id));

        $featuredArticle = (clone $articleQuery)
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->first();

        return Inertia::render('Journal/Index', [
            'seo' => [
                'title' => $selectedCategory?->seo_title ?: ($selectedCategory ? "{$selectedCategory->name} Blog" : 'Dubai Travel Planning Tips | Acute Tourism Blog'),
                'description' => $selectedCategory?->seo_description
                    ?: ($selectedCategory?->description ?: 'Read practical Dubai travel planning tips, visa guidance, itinerary ideas, tour recommendations, and holiday planning advice from Acute Tourism.'),
            ],
            'featuredArticle' => $featuredArticle ? [
                'title' => $featuredArticle->title,
                'slug' => $featuredArticle->slug,
                'category' => $featuredArticle->category_name,
                'categorySlug' => $featuredArticle->blogCategory?->slug,
                'excerpt' => $featuredArticle->excerpt,
                'readTime' => "{$featuredArticle->read_time} min",
                'publishedAt' => optional($featuredArticle->published_at)->format('F j, Y'),
                'heroImageUrl' => $featuredArticle->hero_image_url,
            ] : null,
            'categories' => BlogCategory::query()
                ->active()
                ->withCount(['articles' => fn ($query) => $query->published()])
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get()
                ->map(fn (BlogCategory $category) => [
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'description' => $category->description,
                    'articleCount' => $category->articles_count,
                ]),
            'selectedCategory' => $selectedCategory ? [
                'name' => $selectedCategory->name,
                'slug' => $selectedCategory->slug,
                'description' => $selectedCategory->description,
            ] : null,
            'articles' => (clone $articleQuery)
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->get()
                ->map(fn (Article $article) => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->category_name,
                    'categorySlug' => $article->blogCategory?->slug,
                    'excerpt' => $article->excerpt,
                    'readTime' => "{$article->read_time} min",
                    'publishedAt' => optional($article->published_at)->format('F j, Y'),
                    'heroImageUrl' => $article->hero_image_url,
                ]),
        ]);
    }

    public function article(string $slug): Response
    {
        $article = Article::query()
            ->with('blogCategory')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedArticles = Article::query()
            ->with('blogCategory')
            ->published()
            ->where('id', '!=', $article->id)
            ->when(
                $article->blog_category_id,
                fn ($query) => $query->where('blog_category_id', $article->blog_category_id),
                fn ($query) => $query->where('category', $article->category),
            )
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Article $item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'category' => $item->category_name,
                'categorySlug' => $item->blogCategory?->slug,
                'excerpt' => $item->excerpt,
                'readTime' => "{$item->read_time} min",
            ]);

        return Inertia::render('Journal/Show', [
            'seo' => [
                'title' => $article->seo_title ?: $article->title,
                'description' => $article->seo_description ?: $article->excerpt,
                'image' => $article->hero_image_url,
            ],
            'article' => [
                'title' => $article->title,
                'category' => $article->category_name,
                'categorySlug' => $article->blogCategory?->slug,
                'excerpt' => $article->excerpt,
                'content' => $article->content,
                'readTime' => "{$article->read_time} min",
                'publishedAt' => optional($article->published_at)->format('F j, Y'),
                'heroImageUrl' => $article->hero_image_url,
            ],
            'relatedArticles' => $relatedArticles,
        ]);
    }

    public function faq(): Response
    {
        return Inertia::render('Faq', [
            'seo' => [
                'title' => 'FAQs | Acute Tourism',
                'description' => 'Find answers to common questions about Acute Tourism tours, tickets, holiday packages, visa assistance, Panoramic Bus, corporate events, bookings, payments, cancellations, and refunds.',
            ],
            'featuredFaqs' => Faq::query()
                ->published()
                ->where('is_featured', true)
                ->orderBy('sort_order')
                ->get()
                ->map(fn (Faq $faq) => [
                    'question' => $faq->question,
                    'answer' => $faq->answer,
                    'category' => $faq->category,
                ]),
            'faqGroups' => Faq::query()
                ->published()
                ->orderBy('category')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('category')
                ->map(fn ($items, string $category) => [
                    'category' => $category,
                    'items' => $items->map(fn (Faq $faq) => [
                        'question' => $faq->question,
                        'answer' => $faq->answer,
                    ])->values(),
                ])
                ->values(),
        ]);
    }

    private function renderExperiencesIndex(?string $locationFilter = null, ?string $typeFilter = null): Response
    {
        $locationFilters = $this->experienceLocationFilters();
        $typeFilters = $this->experienceTypeFilters();

        $allExperiences = Experience::query()
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('price_from')
            ->get()
            ->map(fn (Experience $experience) => [
                'type' => 'experience',
                'title' => $experience->title,
                'slug' => $experience->slug,
                'href' => route('experiences.show', $experience->slug),
                'category' => $experience->category,
                'duration' => $experience->duration,
                'location' => $experience->location,
                'summary' => $experience->short_description,
                'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                'heroImageUrl' => $experience->hero_image_url,
            ])
            ->merge(Tour::query()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('price_from')
                ->get()
                ->map(fn (Tour $tour) => [
                    'type' => 'tour',
                    'title' => $tour->title,
                    'slug' => $tour->slug,
                    'href' => route('tours.show', $tour->slug),
                    'category' => $tour->category,
                    'duration' => $tour->duration,
                    'location' => $tour->location,
                    'summary' => $tour->short_description,
                    'priceFrom' => $this->formatMoney($tour->price_from, $tour->currency),
                    'heroImageUrl' => $tour->hero_image_url,
                ]))
            ->filter(function (array $experience) use ($locationFilter, $typeFilter) {
                if ($locationFilter && $this->experienceLocationKey($experience) !== $locationFilter) {
                    return false;
                }

                if ($typeFilter && $this->experienceTypeKey($experience) !== $typeFilter) {
                    return false;
                }

                return true;
            })
            ->values();

        $pageLabel = $typeFilter
            ? ($typeFilters[$typeFilter] ?? 'Tours & Tickets')
            : ($locationFilter ? ($locationFilters[$locationFilter] ?? 'Tours & Tickets') : 'Tours & Tickets');

        return Inertia::render('Experiences/Index', [
            'seo' => [
                'title' => $pageLabel === 'Tours & Tickets' ? 'Dubai Tours and Tickets with Local Support | Acute Tourism' : "{$pageLabel} | Tours & Tickets",
                'description' => $pageLabel === 'Tours & Tickets'
                    ? 'Book Dubai tours and attraction tickets with Acute Tourism, including desert safaris, yacht experiences, water activities, city tours, and selected Dubai attractions.'
                    : "Browse {$pageLabel} with Acute Tourism. Compare trusted activities, tours, tickets, duration, and prices before booking.",
            ],
            'activeCategory' => $typeFilter ?? '',
            'activeLocation' => $locationFilter ?? 'all',
            'activeType' => $typeFilter ?? 'all',
            'pageTitle' => $pageLabel === 'Tours & Tickets'
                ? 'Dubai Tours and Tickets'
                : $pageLabel,
            'pageDescription' => $pageLabel === 'Tours & Tickets'
                ? 'Compare top-rated activities by location, attraction type, price, reviews, and duration, then book the experience that fits your day.'
                : "Browse all available {$pageLabel} options, compare timings and pricing, then open the tour or ticket that fits your plans.",
            'categories' => Experience::query()
                ->where('is_active', true)
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'locationFilters' => collect($locationFilters)
                ->map(fn (string $label, string $key) => [
                    'key' => $key,
                    'label' => $label,
                    'href' => $key === 'all' ? route('experiences.index') : route('experiences.location', $key),
                ])
                ->values(),
            'typeFilters' => collect($typeFilters)
                ->map(fn (string $label, string $key) => [
                    'key' => $key,
                    'label' => $label,
                    'href' => $key === 'all' ? route('experiences.index') : route('experiences.category', $key),
                ])
                ->values(),
            'experiences' => $allExperiences,
        ]);
    }

    private function experienceLocationFilters(): array
    {
        return [
            'all' => 'All Locations',
            'dubai' => 'Dubai',
            'abu-dhabi' => 'Abu Dhabi',
            'other-emirates' => 'Other Emirates',
        ];
    }

    private function experienceTypeFilters(): array
    {
        return [
            'all' => 'All Activities',
            'entry-tickets' => 'Entry Tickets',
            'desert-safari' => 'Desert Safari',
            'city-tours' => 'City Tours',
            'water-sports' => 'Water Sports',
            'water-parks' => 'Water Parks',
            'theme-parks' => 'Theme Parks',
            'yacht-cruises' => 'Yacht & Cruises',
        ];
    }

    private function normalizedExperienceText(array $experience): string
    {
        return Str::of(implode(' ', [
            $experience['title'] ?? '',
            $experience['category'] ?? '',
            $experience['location'] ?? '',
            $experience['summary'] ?? '',
        ]))->lower()->toString();
    }

    private function experienceLocationKey(array $experience): string
    {
        $text = $this->normalizedExperienceText($experience);

        if (str_contains($text, 'abu dhabi') || str_contains($text, 'ferrari world') || str_contains($text, 'yas island')) {
            return 'abu-dhabi';
        }

        if (str_contains($text, 'sharjah') || str_contains($text, 'ras al khaimah') || str_contains($text, 'fujairah') || str_contains($text, 'ajman')) {
            return 'other-emirates';
        }

        return 'dubai';
    }

    private function experienceTypeKey(array $experience): string
    {
        $text = $this->normalizedExperienceText($experience);

        if (str_contains($text, 'desert') || str_contains($text, 'safari') || str_contains($text, 'quad') || str_contains($text, 'buggy')) {
            return 'desert-safari';
        }

        if (str_contains($text, 'city') || str_contains($text, 'landmark') || str_contains($text, 'chauffeur')) {
            return 'city-tours';
        }

        if (str_contains($text, 'jet ski') || str_contains($text, 'parasail') || str_contains($text, 'water sport')) {
            return 'water-sports';
        }

        if (str_contains($text, 'water park') || str_contains($text, 'aquaventure') || str_contains($text, 'wild wadi')) {
            return 'water-parks';
        }

        if (str_contains($text, 'theme park') || str_contains($text, 'ferrari') || str_contains($text, 'img world') || str_contains($text, 'warner')) {
            return 'theme-parks';
        }

        if (str_contains($text, 'yacht') || str_contains($text, 'cruise') || str_contains($text, 'marina')) {
            return 'yacht-cruises';
        }

        if (str_contains($text, 'ticket') || str_contains($text, 'entry') || str_contains($text, 'pass')) {
            return 'entry-tickets';
        }

        return 'entry-tickets';
    }

    protected function interestForCategory(string $category): string
    {
        return match ($category) {
            'Desert' => 'Private Desert',
            'Yacht' => 'Yacht Experience',
            'City' => 'City Tour',
            'Water & Family' => 'Family Experience',
            default => 'General Planning',
        };
    }

    protected function formatMoney(mixed $amount, string $currency): ?string
    {
        if ($amount === null) {
            return null;
        }

        return "{$currency} ".number_format((float) $amount, 0);
    }

    protected function packageShowcaseImage(Package $package, int $index): ?string
    {
        if ($package->hero_image_url) {
            return $package->hero_image_url;
        }

        if ($index > 0 && ! empty($package->gallery_image_urls[$index - 1])) {
            return $package->gallery_image_urls[$index - 1];
        }

        $curatedImages = [
            'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1504215680853-026ed2a45def?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=1600&q=80',
            'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=1600&q=80',
        ];

        return $curatedImages[$index % count($curatedImages)] ?? $package->hero_image_url;
    }

    protected function packageGalleryShowcaseImages(Package $package): array
    {
        if (! empty($package->gallery_image_urls)) {
            return $package->gallery_image_urls;
        }

        $slugBasedGalleries = [
            'ufc-fight-night-returns-to-abu-dhabi' => [
                'https://images.unsplash.com/photo-1544737151-6e4b8f7d5a0b?auto=format&fit=crop&w=1800&q=80',
                'https://images.unsplash.com/photo-1521412644187-c49fa049e84d?auto=format&fit=crop&w=1800&q=80',
                'https://images.unsplash.com/photo-1471295253337-3ceaaedca402?auto=format&fit=crop&w=1800&q=80',
                'https://images.unsplash.com/photo-1461896836934-ffe607ba8211?auto=format&fit=crop&w=1800&q=80',
            ],
        ];

        if (isset($slugBasedGalleries[$package->slug])) {
            return $slugBasedGalleries[$package->slug];
        }

        $curatedGalleryPool = [
            'https://images.unsplash.com/photo-1512453979798-5ea266f8880c?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1504215680853-026ed2a45def?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1533105079780-92b9be482077?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1476514525535-07fb3b4ae5f1?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1526481280695-3c4690d2f038?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1513673054901-2b5f51551112?auto=format&fit=crop&w=1800&q=80',
            'https://images.unsplash.com/photo-1496564203457-11bb12075d90?auto=format&fit=crop&w=1800&q=80',
        ];

        $offset = abs(crc32((string) $package->slug)) % count($curatedGalleryPool);
        $images = [];

        for ($i = 0; $i < 4; $i++) {
            $images[] = $curatedGalleryPool[($offset + $i) % count($curatedGalleryPool)];
        }

        return $images;
    }

    protected function mediaItems(?string $heroVideoUrl, ?string $heroImageUrl, array $galleryVideoUrls, array $galleryImageUrls): array
    {
        $items = [];

        if ($heroVideoUrl) {
            $items[] = ['type' => 'video', 'url' => $heroVideoUrl];
        }

        if ($heroImageUrl) {
            $items[] = ['type' => 'image', 'url' => $heroImageUrl];
        }

        foreach ($galleryVideoUrls as $url) {
            $items[] = ['type' => 'video', 'url' => $url];
        }

        foreach ($galleryImageUrls as $url) {
            $items[] = ['type' => 'image', 'url' => $url];
        }

        return collect($items)
            ->unique(fn (array $item) => $item['type'].'|'.$item['url'])
            ->values()
            ->all();
    }

    protected function pricedBookingOptions(?array $options, string $currency = 'AED'): array
    {
        return collect($options ?? [])
            ->filter(fn ($option) => is_array($option) && filled($option['label'] ?? null) && is_numeric($option['price'] ?? null))
            ->values()
            ->map(function (array $option, int $index) use ($currency) {
                $label = trim((string) $option['label']);
                $amount = (float) $option['price'];
                $key = Str::slug($label) ?: "option-{$index}";

                return [
                    'key' => "{$key}-{$index}",
                    'label' => $label,
                    'description' => filled($option['description'] ?? null) ? trim((string) $option['description']) : null,
                    'amountValue' => $amount,
                    'amount' => $this->formatMoney($amount, $currency),
                    'childAmountValue' => is_numeric($option['child_price'] ?? null) ? (float) $option['child_price'] : null,
                    'childAmount' => is_numeric($option['child_price'] ?? null) ? $this->formatMoney((float) $option['child_price'], $currency) : null,
                ];
            })
            ->all();
    }

    protected function availabilityPayload(Experience|Tour $payable): array
    {
        return [
            'unavailableDates' => collect($payable->unavailable_dates ?? [])
                ->filter(fn ($date) => is_string($date) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date))
                ->unique()
                ->values()
                ->all(),
            'unavailablePeriods' => collect($payable->unavailable_periods ?? [])
                ->filter(fn ($period) => is_array($period) && filled($period['start'] ?? null) && filled($period['end'] ?? null))
                ->map(fn (array $period) => [
                    'start' => (string) $period['start'],
                    'end' => (string) $period['end'],
                    'label' => filled($period['label'] ?? null) ? (string) $period['label'] : null,
                ])
                ->values()
                ->all(),
        ];
    }

    protected function averageRating($reviews): float
    {
        if ($reviews->isEmpty()) {
            return 5.0;
        }

        return round((float) $reviews->avg('rating'), 1);
    }

    protected function presentReviews($reviews): array
    {
        return $reviews
            ->take(6)
            ->map(fn ($review) => [
                'authorName' => $review->author_name,
                'rating' => $review->rating,
                'title' => $review->title,
                'quote' => $review->quote,
                'tag' => $review->tag,
                'source' => $review->source,
                'reviewedAt' => optional($review->reviewed_at)->format('F Y'),
            ])
            ->values()
            ->all();
    }
}
