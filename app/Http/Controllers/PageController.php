<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Package;
use App\Models\SiteSetting;
use App\Models\Tour;
use Illuminate\Http\Request;
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
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->limit(3)
            ->get()
            ->values()
            ->map(fn (Package $package, int $index) => [
                'title' => $package->title,
                'slug' => $package->slug,
                'summary' => $package->short_description,
                'duration' => $package->duration,
                'location' => $package->location,
                'priceFrom' => $this->formatMoney($package->price_from, $package->currency),
                'heroImageUrl' => $this->packageShowcaseImage($package, $index),
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
                'href' => route('visa.index').'#uk-visa',
            ],
            [
                'title' => 'USA Visa',
                'slug' => 'usa-visa',
                'summary' => 'Profile review and application support focused on B1/B2 visitor intent and interview readiness.',
                'tag' => 'High Intent',
                'href' => route('visa.index').'#usa-visa',
            ],
            [
                'title' => 'Japan Visa',
                'slug' => 'japan-visa',
                'summary' => 'Travel plan and documentation support for short-term Japan tourist applications.',
                'tag' => 'Tourist',
                'href' => route('visa.index').'#japan-visa',
            ],
            [
                'title' => 'Canada Visa',
                'slug' => 'canada-visa',
                'summary' => 'Visitor visa assistance with strong document preparation and expectation setting.',
                'tag' => 'Visitor',
                'href' => route('visa.index').'#canada-visa',
            ],
            [
                'title' => 'Australia Visa',
                'slug' => 'australia-visa',
                'summary' => 'UAE residents: visitor visas with GTE-aware documentation and realistic timelines.',
                'tag' => 'Pacific',
                'href' => route('visa.index').'#australia-visa',
            ],
            [
                'title' => 'Malaysia Visa',
                'slug' => 'malaysia-visa',
                'summary' => 'Tourist and eVisa routes from the UAE—eligibility, checklist, and submission support.',
                'tag' => 'Southeast Asia',
                'href' => route('visa.index').'#malaysia-visa',
            ],
            [
                'title' => 'Vietnam Visa',
                'slug' => 'vietnam-visa',
                'summary' => 'eVisa and tourist visas with itinerary-aligned documents and clear application steps.',
                'tag' => 'Southeast Asia',
                'href' => route('visa.index').'#vietnam-visa',
            ],
            [
                'title' => 'E-Visa Assistance',
                'slug' => 'evisa-assistance',
                'summary' => 'Fast-turn support for online visa workflows where document clarity and submission accuracy matter.',
                'tag' => 'Fast Track',
                'href' => route('visa.index').'#evisa-assistance',
            ],
        ]);

        $mustDoExperiences = $featuredExperiences->take(6);
        $topRatedExperiences = $featuredExperiences->slice(6)->values();

        return Inertia::render('Home', [
            'seo' => [
                'title' => 'Exclusively Curated Holiday Experiences',
                'description' => 'Luxury travel planning, premium Dubai experiences, and professional visa services from one polished travel brand.',
            ],
            'hero' => [
                'eyebrow' => 'Tailor-Made Dubai & UAE Holidays',
                'title' => 'Exclusively Curated Holiday Experiences',
                'description' => 'Crafted by destination experts, each itinerary is designed to deliver a refined and effortless journey with luxury hotels, private transfers, bespoke desert safaris, and priority access to iconic landmarks.',
                'heroImageUrl' => 'https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=2000&q=80',
                'videoUrl' => 'https://acutetourism.org/videos/hero-section-intro.mp4',
                'primaryCta' => ['label' => 'Explore Packages', 'href' => route('packages.index')],
                'secondaryCta' => ['label' => 'Visa Services', 'href' => route('visa.index')],
                'tertiaryCta' => ['label' => 'Contact Us', 'href' => route('contact')],
            ],
            'homeSections' => [
                'ribbonEyebrow' => 'In Dubai & beyond',
                'ribbonTitle' => 'Curated Travel Moments',
                'collectionsEyebrow' => 'Destinations',
                'collectionsTitle' => 'Explore Other Emirates',
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
            'recommendations' => $visaServices,
            'serviceFocus' => [
                [
                    'title' => 'Visa Services',
                    'copy' => 'Schengen, UK, USA, Canada, Japan, and more, clear requirements, document checks, and step-by-step guidance before you book your trip.',
                    'href' => route('visa.index'),
                    'cta' => 'View visa services',
                    'tag' => 'Visa assistance',
                ],
                [
                    'title' => 'International Tour Packages',
                    'copy' => 'Hand-picked holidays with hotels, transfers, and activities coordinated for you, fewer moving parts, more time to enjoy the journey.',
                    'href' => route('packages.index'),
                    'cta' => 'Explore packages',
                    'tag' => 'Holiday packages',
                ],
                [
                    'title' => 'Luxury Experiences in Dubai',
                    'copy' => 'Private desert camps, yacht days, and city experiences with concierge-style coordination, built around your dates and group.',
                    'href' => route('experiences.index'),
                    'cta' => 'Browse experiences',
                    'tag' => 'Dubai & UAE',
                ],
            ],
            'testimonials' => [
                [
                    'name' => 'Sarah Al-Mansoori',
                    'tag' => 'Dubai Desert Experience',
                    'quote' => 'Seamless pickup, incredible camp setup, and a team that actually answers WhatsApp. Felt like a private concierge from start to finish.',
                ],
                [
                    'name' => 'James Porter',
                    'tag' => 'Schengen visa support',
                    'quote' => 'Clear checklist, realistic timelines, and no guesswork at the VFS appointment. We had our visas back without the stress we expected.',
                ],
                [
                    'name' => 'Elena Rossi',
                    'tag' => 'UAE package holiday',
                    'quote' => 'Hotels, transfers, and desert day were coordinated in one flow. One invoice, one point of contact, exactly what we wanted for a family trip.',
                ],
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
                ['emoji' => 'UK', 'title' => 'UK Visa', 'copy' => 'United Kingdom support', 'href' => route('visa.index').'#uk-visa'],
                ['emoji' => 'US', 'title' => 'USA Visa', 'copy' => 'Visitor and profile review', 'href' => route('visa.index').'#usa-visa'],
                ['emoji' => 'JP', 'title' => 'Japan Visa', 'copy' => 'Short-stay planning', 'href' => route('visa.index').'#japan-visa'],
                ['emoji' => 'CA', 'title' => 'Canada Visa', 'copy' => 'Visitor visa assistance', 'href' => route('visa.index').'#canada-visa'],
                ['emoji' => 'BR', 'title' => 'Brazil Visa', 'copy' => 'Travel document support', 'href' => route('visa.index').'#brazil-visa'],
                ['emoji' => 'ZA', 'title' => 'South Africa Visa', 'copy' => 'Tourist application guidance', 'href' => route('visa.index').'#south-africa-visa'],
                ['emoji' => 'EV', 'title' => 'E-Visa Assistance', 'copy' => 'Online processing routes', 'href' => route('visa.index').'#evisa-assistance'],
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

    public function experiences(Request $request): Response
    {
        $activeCategory = $request->string('category')->toString();

        $query = Experience::query()->where('is_active', true);

        if ($activeCategory !== '') {
            $query->where('category', $activeCategory);
        }

        return Inertia::render('Experiences/Index', [
            'seo' => [
                'title' => 'Experiences',
                'description' => 'Browse premium Dubai experiences across desert, yacht, city, and family categories.',
            ],
            'activeCategory' => $activeCategory,
            'categories' => Experience::query()
                ->where('is_active', true)
                ->select('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category'),
            'experiences' => $query
                ->orderByDesc('is_featured')
                ->orderBy('price_from')
                ->get()
                ->map(fn (Experience $experience) => [
                    'title' => $experience->title,
                    'slug' => $experience->slug,
                    'category' => $experience->category,
                    'duration' => $experience->duration,
                    'summary' => $experience->short_description,
                    'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                    'heroImageUrl' => $experience->hero_image_url,
                ]),
        ]);
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
                'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                'isPrivate' => $experience->is_private,
                'highlights' => $experience->highlights ?? [],
                'inclusions' => $experience->inclusions ?? [],
                'exclusions' => $experience->exclusions ?? [],
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
            ->with(['experiences' => fn ($query) => $query->where('is_active', true)->orderBy('collection_experience.sort_order')])
            ->firstOrFail();

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
                'experiences' => $collection->experiences->map(fn (Experience $experience) => [
                    'title' => $experience->title,
                    'slug' => $experience->slug,
                    'category' => $experience->category,
                    'priceFrom' => $this->formatMoney($experience->price_from, $experience->currency),
                    'duration' => $experience->duration,
                    'heroImageUrl' => $experience->hero_image_url,
                ]),
            ],
        ]);
    }

    public function packages(): Response
    {
        $packages = Package::query()
            ->where('is_active', true)
            ->orderByDesc('is_featured')
            ->orderBy('title')
            ->get()
            ->values();

        return Inertia::render('Packages/Index', [
            'seo' => [
                'title' => 'Packages',
                'description' => 'Curated Dubai and Abu Dhabi travel packages combining experiences, stays, and events.',
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
                ]),
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
                'priceFrom' => $this->formatMoney($tour->price_from, $tour->currency),
                'isPrivate' => $tour->is_private,
                'highlights' => $tour->highlights ?? [],
                'inclusions' => $tour->inclusions ?? [],
                'exclusions' => $tour->exclusions ?? [],
                'averageRating' => $this->averageRating($tour->reviews),
                'reviewCount' => $tour->reviews->count(),
                'reviews' => $this->presentReviews($tour->reviews),
            ],
            'relatedTours' => $relatedTours,
        ]);
    }

    public function schengenVisa(): Response
    {
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
                    'href' => url('/packages'),
                    'cta' => 'View packages',
                ],
                [
                    'eyebrow' => 'Approvals',
                    'title' => 'Visa consultation',
                    'copy' => 'End-to-end support for Schengen and other routes with document checks and application guidance.',
                    'href' => url('/visa-services'),
                    'cta' => 'All visa services',
                ],
                [
                    'eyebrow' => 'Experiences',
                    'title' => 'Dubai experiences',
                    'copy' => 'Tours, desert safaris, and itinerary planning for travelers visiting the UAE.',
                    'href' => url('/experiences'),
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
                ['code' => 'UK', 'title' => 'UK Visa', 'subtitle' => 'United Kingdom', 'href' => url('/visa-services#uk-visa')],
                ['code' => 'US', 'title' => 'USA Visa', 'subtitle' => 'United States', 'href' => url('/visa-services#usa-visa')],
                ['code' => 'JP', 'title' => 'Japan Visa', 'subtitle' => 'Japan', 'href' => url('/visa-services#japan-visa')],
                ['code' => 'CA', 'title' => 'Canada Visa', 'subtitle' => 'Canada', 'href' => url('/visa-services#canada-visa')],
                ['code' => 'ZA', 'title' => 'South Africa Visa', 'subtitle' => 'South Africa', 'href' => url('/visa-services#south-africa-visa')],
                ['code' => 'AU', 'title' => 'Australia Visa', 'subtitle' => 'Australia', 'href' => url('/visa-services#australia-visa')],
                ['code' => 'MY', 'title' => 'Malaysia Visa', 'subtitle' => 'Malaysia', 'href' => url('/visa-services#malaysia-visa')],
                ['code' => 'VN', 'title' => 'Vietnam Visa', 'subtitle' => 'Vietnam', 'href' => url('/visa-services#vietnam-visa')],
                ['code' => 'TR', 'title' => 'Turkey Visa', 'subtitle' => 'Turkey', 'href' => url('/visa-services')],
                ['code' => 'EV', 'title' => 'Other eVisa help', 'subtitle' => 'Online routes', 'href' => url('/visa-services#evisa-assistance')],
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

    public function visaServices(): Response
    {
        $settings = SiteSetting::current();

        return Inertia::render('VisaServices', [
            'seo' => [
                'title' => 'Visa Services',
                'description' => 'Professional visa services for Schengen, UK, USA, Japan, Canada, Australia, Malaysia, Vietnam, Brazil, South Africa, and e-visa assistance.',
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
                'title' => 'Visa support for the destinations travellers ask for most.',
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
                ],
                [
                    'id' => 'usa-visa',
                    'title' => 'USA Visa',
                    'tag' => 'Interview',
                    'copy' => 'B1/B2 visitor-focused guidance for profile review, supporting file preparation, and expectation setting.',
                ],
                [
                    'id' => 'japan-visa',
                    'title' => 'Japan Visa',
                    'tag' => 'Travel Planning',
                    'copy' => 'Short-stay Japan visitor visa support with a stronger focus on itinerary structure and documentation consistency.',
                ],
                [
                    'id' => 'canada-visa',
                    'title' => 'Canada Visa',
                    'tag' => 'Visitor',
                    'copy' => 'Tourist and family-visit support with a cleaner narrative around financial proof, residence status, and travel purpose.',
                ],
                [
                    'id' => 'evisa-assistance',
                    'title' => 'E-visa Assistance',
                    'tag' => 'Online Processing',
                    'copy' => 'Online application support for destinations where submission speed and data accuracy matter.',
                ],
                [
                    'id' => 'brazil-visa',
                    'title' => 'Brazil Visa',
                    'tag' => 'Tourist',
                    'copy' => 'Application planning and supporting-document guidance for travelers heading to Brazil.',
                ],
                [
                    'id' => 'south-africa-visa',
                    'title' => 'South Africa Visa',
                    'tag' => 'Tourist',
                    'copy' => 'Visa support for South Africa itineraries with practical document preparation and travel-purpose framing.',
                ],
                [
                    'id' => 'australia-visa',
                    'title' => 'Australia Visa',
                    'tag' => 'Visitor',
                    'copy' => 'Visitor and short-stay Australia visas for UAE residents with documentation support, Genuine Temporary Entrant (GTE) framing, and realistic embassy timelines.',
                ],
                [
                    'id' => 'malaysia-visa',
                    'title' => 'Malaysia Visa',
                    'tag' => 'Tourist / eVisa',
                    'copy' => 'Malaysia tourist and eVisa routes from the UAE: eligibility checks, checklist-led preparation, and submission support.',
                ],
                [
                    'id' => 'vietnam-visa',
                    'title' => 'Vietnam Visa',
                    'tag' => 'Tourist / eVisa',
                    'copy' => 'Vietnam eVisa and tourist routes with itinerary-aligned documents and clear steps from application to approval.',
                ],
                [
                    'id' => 'tourist-visa-assistance',
                    'title' => 'Tourist Visa Assistance',
                    'tag' => 'General Support',
                    'copy' => 'A broader consultation route for travelers who are unsure which visa process applies to their trip.',
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
                'title' => 'About',
                'description' => 'Travel smart. Travel seamless. Travel with Acute Tourism LLC.',
            ],
            'pillars' => [
                'Refined travel experiences across Dubai and the UAE with precision and attention to detail.',
                'Luxury hotel reservations, private airport transfers, guided tours, and exclusive desert experiences.',
                'Strong local network for priority access, competitive value, and personalized itineraries.',
            ],
        ]);
    }

    public function cancellationPolicy(): Response
    {
        return Inertia::render('CancellationPolicy', [
            'seo' => [
                'title' => 'Cancellation Policy',
                'description' => 'Review the Acute Tourism cancellation and refund policy for experiences, tours, packages, and related travel services.',
            ],
            'policySections' => [
                [
                    'title' => 'General Booking Policy',
                    'body' => 'Cancellation terms can vary by product, supplier, transport arrangement, accommodation component, and how close the booking is to the service date. Acute Tourism aims to present the applicable terms as clearly as possible before payment or final confirmation.',
                ],
                [
                    'title' => 'Experiences and Tours',
                    'body' => 'Many standard experiences and tours may qualify for a full refund when cancelled at least 24 hours before the scheduled start time. Some products, however, may have stricter conditions because of ticket issuance, limited-capacity inventory, or third-party operating rules.',
                ],
                [
                    'title' => 'Packages and Multi-Service Bookings',
                    'body' => 'Packages that combine hotels, transfers, attraction tickets, or special event access may follow supplier-specific rules. Once any non-refundable component is committed, the refundable amount can change even if the overall booking is cancelled before travel.',
                ],
                [
                    'title' => 'Late Cancellations and No-Shows',
                    'body' => 'Cancellations made after the applicable cut-off time, failure to arrive at the pickup point, or missed participation because of delayed customer response may be treated as non-refundable unless the supplier confirms otherwise.',
                ],
                [
                    'title' => 'Date Changes and Amendments',
                    'body' => 'Requests to reschedule are handled case by case. Amendments depend on availability, the original supplier terms, and whether any new cost applies on the revised date. A change request should not be treated as confirmed until Acute Tourism provides written confirmation.',
                ],
                [
                    'title' => 'Refund Processing',
                    'body' => 'Where a refund is approved, the processing time depends on the payment method and the external provider involved. Acute Tourism can confirm the refund status from our side, but final posting time is also affected by the card issuer or payment channel.',
                ],
                [
                    'title' => 'How to Request a Cancellation',
                    'body' => 'To request a cancellation, reply using your booking reference or contact the Acute Tourism team directly with the lead traveler name, service date, and the item booked. This helps us validate the file and apply the correct supplier rule quickly.',
                ],
            ],
        ]);
    }

    public function termsAndConditions(): Response
    {
        return Inertia::render('TermsAndConditions', [
            'seo' => [
                'title' => 'Terms and Conditions',
                'description' => 'Review the Acute Tourism terms and conditions governing use of the website, bookings, payments, and related travel services.',
            ],
            'termsSections' => [
                [
                    'title' => 'Use of This Website',
                    'body' => 'By accessing the Acute Tourism website, you agree to use it for lawful purposes only. The information on the site is intended to support travel discovery, planning, and booking inquiries, and should not be used in a way that disrupts site access, misrepresents intent, or interferes with operations.',
                ],
                [
                    'title' => 'Bookings and Availability',
                    'body' => 'All products, services, pricing, timing, and inclusions are subject to confirmation. A listing on the site does not guarantee final availability until the booking has been accepted, supplier space has been confirmed where required, and payment or deposit obligations have been satisfied.',
                ],
                [
                    'title' => 'Pricing and Payment',
                    'body' => 'Displayed pricing may change because of supplier revisions, ticket availability, seasonal demand, special-event dates, accommodation rules, or transport updates. Where a payment is required, the customer is responsible for ensuring that the traveler details, dates, and selected product are correct before submission.',
                ],
                [
                    'title' => 'Product Descriptions',
                    'body' => 'Acute Tourism aims to present accurate information about experiences, tours, packages, and travel services. However, some operational elements may change after publication, including route order, meeting points, duration, language arrangement, pickup windows, or supplier-managed inclusions.',
                ],
                [
                    'title' => 'Customer Responsibilities',
                    'body' => 'Customers are responsible for providing correct contact details, traveler names, date preferences, and any relevant travel information requested during inquiry or booking. Where identification, visas, attraction tickets, or arrival timing matter, the customer is responsible for providing accurate supporting details on time.',
                ],
                [
                    'title' => 'Changes, Cancellations, and Refunds',
                    'body' => 'Cancellation, amendment, and refund handling are governed by the applicable product terms, supplier rules, and payment conditions. Customers should review the dedicated cancellation policy and any product-specific conditions shown before purchase or final confirmation.',
                ],
                [
                    'title' => 'Third-Party Suppliers',
                    'body' => 'Many services offered through Acute Tourism rely on third-party providers such as attraction operators, transport companies, hotels, ticketing partners, and event suppliers. Acute Tourism coordinates these arrangements, but some performance obligations, restrictions, and operational decisions remain under the control of those providers.',
                ],
                [
                    'title' => 'Limitation of Liability',
                    'body' => 'Acute Tourism is not responsible for loss or disruption caused by customer-provided errors, late arrival, force majeure events, government restrictions, attraction closures, transport incidents, supplier interruptions, or any event outside reasonable operational control. Liability is limited to the extent permitted by applicable law.',
                ],
                [
                    'title' => 'Intellectual Property and Content',
                    'body' => 'The branding, layout, written copy, and site materials used on the Acute Tourism website may not be copied, republished, or reused for commercial purposes without permission, except where usage is otherwise permitted by law.',
                ],
                [
                    'title' => 'Contact and Policy Updates',
                    'body' => 'Acute Tourism may update these terms from time to time as the business, products, payment flow, or supplier structure evolves. Customers should review the latest version before submitting a booking or inquiry if they want the current terms on record.',
                ],
            ],
        ]);
    }

    public function privacyPolicy(): Response
    {
        return Inertia::render('PrivacyPolicy', [
            'seo' => [
                'title' => 'Privacy Policy',
                'description' => 'Review how Acute Tourism collects, uses, stores, and protects personal information provided through the website and booking process.',
            ],
            'privacySections' => [
                [
                    'title' => 'Information We Collect',
                    'body' => 'Acute Tourism may collect personal information when you submit an inquiry, request a booking, complete a checkout, contact the team, or otherwise interact with the website. This may include your name, email address, phone number, travel date, guest count, and any trip-related details you provide voluntarily.',
                ],
                [
                    'title' => 'How We Use Your Information',
                    'body' => 'The information you provide is used to respond to inquiries, prepare travel quotations, coordinate bookings, issue confirmations, process payments, support customer communication, and improve the operational quality of the services offered through Acute Tourism.',
                ],
                [
                    'title' => 'Bookings and Supplier Coordination',
                    'body' => 'Where necessary to fulfill a request, relevant customer details may be shared with third-party suppliers such as hotels, attraction operators, transport providers, ticketing partners, and related service vendors. Only the information reasonably required for service delivery should be shared for that purpose.',
                ],
                [
                    'title' => 'Payments and Transaction Data',
                    'body' => 'Payment-related information may be processed through external payment providers or gateways. Acute Tourism may retain booking references, transaction records, traveler details, and operational notes needed for reconciliation, customer support, and lawful recordkeeping, but sensitive payment handling may also be governed by the payment provider’s own policies.',
                ],
                [
                    'title' => 'Communication and Follow-Up',
                    'body' => 'If you contact Acute Tourism or submit a booking request, the team may use your details to send confirmations, clarifications, itinerary updates, payment reminders, or operational messages connected to your request. Communication may continue where required to complete or support the service you asked for.',
                ],
                [
                    'title' => 'Cookies and Website Usage',
                    'body' => 'The site may use cookies, session handling, and technical logging to support security, authentication, error handling, and site functionality. These tools help maintain the website and improve user experience, but they also form part of how the platform operates and should be understood as part of normal website use.',
                ],
                [
                    'title' => 'Data Retention',
                    'body' => 'Personal data may be kept for as long as needed to provide requested services, meet operational obligations, resolve disputes, maintain internal records, or comply with legal, accounting, tax, or payment-related requirements.',
                ],
                [
                    'title' => 'Data Protection and Security',
                    'body' => 'Acute Tourism takes reasonable steps to protect information against unauthorized access, misuse, or accidental disclosure. However, no online transmission or storage environment should be treated as risk-free, and customers should avoid sending unnecessary sensitive data through informal channels.',
                ],
                [
                    'title' => 'Third-Party Links and External Services',
                    'body' => 'The website may link to external sites, services, or providers. Once you leave the Acute Tourism website or interact directly with an external platform, the privacy practices of that external party apply and should be reviewed independently.',
                ],
                [
                    'title' => 'Policy Updates and Contact',
                    'body' => 'Acute Tourism may revise this privacy policy as website features, legal obligations, booking systems, or supplier workflows evolve. If you need clarification about how your information is handled, contact the company directly using the published contact details.',
                ],
            ],
        ]);
    }

    public function corporateEvents(): Response
    {
        return Inertia::render('CorporateEvents', [
            'seo' => [
                'title' => 'Corporate Events',
                'description' => 'Executive and incentive experiences for corporate groups in Dubai.',
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
                'title' => 'Contact',
                'description' => 'Contact Acute Tourism for concierge-led Dubai experience planning and group requests.',
            ],
            'contact' => [
                'email' => $settings->contact_email,
                'phone' => $settings->contact_phone,
                'phoneSecondary' => $settings->contact_phone_secondary,
                'address' => $settings->contact_address,
                'whatsappNumber' => $settings->whatsapp_number,
            ],
            'interestOptions' => $settings->interest_options ?? [],
        ]);
    }

    public function journal(): Response
    {
        $featuredArticle = Article::query()
            ->published()
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->first();

        return Inertia::render('Journal/Index', [
            'seo' => [
                'title' => 'Journal',
                'description' => 'Editorial travel content supporting premium Dubai experience discovery.',
            ],
            'featuredArticle' => $featuredArticle ? [
                'title' => $featuredArticle->title,
                'slug' => $featuredArticle->slug,
                'category' => $featuredArticle->category,
                'excerpt' => $featuredArticle->excerpt,
                'readTime' => "{$featuredArticle->read_time} min",
                'publishedAt' => optional($featuredArticle->published_at)->format('F j, Y'),
                'heroImageUrl' => $featuredArticle->hero_image_url,
            ] : null,
            'articles' => Article::query()
                ->published()
                ->orderByDesc('is_featured')
                ->orderBy('sort_order')
                ->orderByDesc('published_at')
                ->get()
                ->map(fn (Article $article) => [
                    'title' => $article->title,
                    'slug' => $article->slug,
                    'category' => $article->category,
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
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedArticles = Article::query()
            ->published()
            ->where('id', '!=', $article->id)
            ->where('category', $article->category)
            ->orderByDesc('is_featured')
            ->orderBy('sort_order')
            ->orderByDesc('published_at')
            ->limit(3)
            ->get()
            ->map(fn (Article $item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'category' => $item->category,
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
                'category' => $article->category,
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
                'title' => 'FAQ',
                'description' => 'Frequently asked questions about the Acute Tourism rebuild and booking approach.',
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
        if ($index === 0 && $package->hero_image_url) {
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
