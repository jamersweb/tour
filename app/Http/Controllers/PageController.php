<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Faq;
use App\Models\Package;
use App\Models\SiteSetting;
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
            ->map(fn (Package $package) => [
                'title' => $package->title,
                'slug' => $package->slug,
                'summary' => $package->short_description,
                'duration' => $package->duration,
                'location' => $package->location,
                'priceFrom' => $this->formatMoney($package->price_from, $package->currency),
                'heroImageUrl' => $package->hero_image_url,
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

        return Inertia::render('Home', [
            'seo' => [
                'title' => $settings->site_name,
                'description' => 'Curated premium Dubai experiences across desert, yacht, city, and private itineraries.',
            ],
            'hero' => [
                'eyebrow' => $settings->home_hero_eyebrow,
                'title' => $settings->home_hero_title,
                'description' => $settings->home_hero_description,
                'videoUrl' => 'https://acutetourism.org/videos/hero-section-intro.mp4',
                'primaryCta' => ['label' => $settings->home_primary_cta_label, 'href' => route('experiences.index')],
                'secondaryCta' => ['label' => $settings->home_secondary_cta_label, 'href' => route('contact')],
            ],
            'homeSections' => [
                'trustHeading' => $settings->home_trust_heading,
                'collectionsEyebrow' => $settings->home_collections_eyebrow,
                'collectionsTitle' => $settings->home_collections_title,
                'featuredEyebrow' => $settings->home_featured_eyebrow,
                'featuredTitle' => $settings->home_featured_title,
                'packagesEyebrow' => 'Featured Packages',
                'packagesTitle' => 'Event-led and stay-inclusive products for higher-intent bookings.',
                'recommendationsEyebrow' => 'Our Recommendation',
                'recommendationsTitle' => 'Start with clearer buying paths, not a crowded catalog.',
            ],
            'stats' => [
                ['label' => 'Experiences', 'value' => Experience::query()->where('is_active', true)->count()],
                ['label' => 'Curated Collections', 'value' => Collection::query()->where('is_featured', true)->count()],
                ['label' => 'Packages', 'value' => Package::query()->where('is_active', true)->count()],
            ],
            'collections' => $featuredCollections,
            'featuredExperiences' => $featuredExperiences,
            'heroGallery' => $heroGallery,
            'packages' => $packages,
            'trustPoints' => $settings->home_trust_points ?? [],
            'recommendations' => [
                [
                    'title' => 'Lead with private and premium experiences',
                    'copy' => 'Keep the homepage focused on high-intent desert, yacht, city, and family products instead of spreading attention across unrelated travel modules.',
                ],
                [
                    'title' => 'Use landing-page structure, not marketplace clutter',
                    'copy' => 'One clear hero, strong social proof, curated collections, 16 flagship experiences, and packages creates a better premium first impression.',
                ],
                [
                    'title' => 'Sell through logistics clarity and concierge trust',
                    'copy' => 'Premium buyers convert faster when pricing, pickup, duration, and follow-up channels feel reliable before checkout starts.',
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
            ->with('collections:id,name,slug')
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
                'galleryImageUrls' => $experience->gallery_image_urls,
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
        return Inertia::render('Packages/Index', [
            'seo' => [
                'title' => 'Packages',
                'description' => 'Curated Dubai and Abu Dhabi travel packages combining experiences, stays, and events.',
            ],
            'packages' => Package::query()
                ->where('is_active', true)
                ->orderByDesc('is_featured')
                ->orderBy('title')
                ->get()
                ->map(fn (Package $package) => [
                    'title' => $package->title,
                    'slug' => $package->slug,
                    'summary' => $package->short_description,
                    'duration' => $package->duration,
                    'location' => $package->location,
                    'priceFrom' => $package->price_from ? "{$package->currency} ".number_format((float) $package->price_from, 0) : null,
                    'heroImageUrl' => $package->hero_image_url,
                ]),
        ]);
    }

    public function package(string $slug): Response
    {
        $package = Package::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        $relatedPackages = Package::query()
            ->where('is_active', true)
            ->where('id', '!=', $package->id)
            ->orderByDesc('is_featured')
            ->limit(3)
            ->get()
            ->map(fn (Package $item) => [
                'title' => $item->title,
                'slug' => $item->slug,
                'summary' => $item->short_description,
                'duration' => $item->duration,
                'heroImageUrl' => $item->hero_image_url,
            ]);

        return Inertia::render('Packages/Show', [
            'seo' => [
                'title' => $package->seo_title ?: $package->title,
                'description' => $package->seo_description ?: $package->short_description,
                'image' => $package->hero_image_url,
            ],
            'packageItem' => [
                'title' => $package->title,
                'slug' => $package->slug,
                'shortDescription' => $package->short_description,
                'description' => $package->description,
                'heroImageUrl' => $package->hero_image_url,
                'galleryImageUrls' => $package->gallery_image_urls,
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
            ],
            'relatedPackages' => $relatedPackages,
        ]);
    }

    public function about(): Response
    {
        return Inertia::render('About', [
            'seo' => [
                'title' => 'About',
                'description' => 'Learn how Acute Tourism is being rebuilt into a premium Dubai experiences brand.',
            ],
            'pillars' => [
                'Curated product selection instead of template catalog sprawl',
                'Clear logistics, pickup, cancellation, and service standards',
                'Higher-end visual presentation aligned with premium Dubai travel',
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
}
