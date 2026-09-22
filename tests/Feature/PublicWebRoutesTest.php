<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\BusTourListing;
use App\Models\BusTourPage;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Package;
use App\Models\StaticPage;
use App\Models\User;
use App\Filament\Resources\BusTourPages\Pages\EditBusTourPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use Tests\TestCase;

class PublicWebRoutesTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    /**
     * @return array<int, string>
     */
    protected function staticGetPaths(): array
    {
        return [
            '/',
            '/acute-landing',
            '/dubai-tours-and-tickets',
            '/dubai-holiday-packages',
            '/tourist-visa-assistance-uae-residents',
            '/schengen-visa',
            '/uk-visa',
            '/usa-visa',
            '/canada-visa',
            '/japan-visa',
            '/australia-visa',
            '/turkey-visa',
            '/malaysia-visa',
            '/vietnam-visa',
            '/brazil-visa',
            '/south-africa-visa',
            '/evisa-assistance',
            '/tourist-visa-assistance',
            '/luxury-bus-tour-dubai',
            '/luxury-bus-tour-dubai/dubai-panoramic-bus-food-tasting',
            '/about',
            '/corporate-travel-event-planning-dubai',
            '/contact',
            '/journal',
            '/faq',
            '/sitemap.xml',
            '/login',
            '/register',
            '/forgot-password',
            '/up',
        ];
    }

    public function test_static_public_get_routes_return_200(): void
    {
        foreach ($this->staticGetPaths() as $path) {
            $response = $this->get($path);
            $response->assertOk(
                "Expected 200 for GET {$path}, got {$response->status()}",
            );
        }
    }

    public function test_seeded_detail_and_checkout_routes_return_200(): void
    {
        Config::set([
            'payments.network.enabled' => true,
            'payments.network.outlet_id' => 'test-outlet',
            'payments.network.api_key' => 'test-api-key',
            'payments.network.api_secret' => 'test-api-secret',
        ]);

        $experience = Experience::query()
            ->where('slug', 'private-heritage-desert-safari')
            ->where('is_active', true)
            ->firstOrFail();

        $package = Package::query()
            ->where('slug', 'ufc-fight-night-returns-to-abu-dhabi')
            ->where('is_active', true)
            ->firstOrFail();

        $collection = Collection::query()
            ->where('slug', 'luxury-desert-safaris')
            ->firstOrFail();

        $article = Article::query()
            ->where('slug', 'private-yacht-charter-dubai-guide')
            ->published()
            ->firstOrFail();

        $pairs = [
            "/experiences/{$experience->slug}",
            "/packages/{$package->slug}",
            "/collections/{$collection->slug}",
            "/journal/{$article->slug}",
            "/checkout/experiences/{$experience->slug}",
            "/checkout/packages/{$package->slug}",
        ];

        foreach ($pairs as $path) {
            $response = $this->get($path);
            $response->assertOk(
                "Expected 200 for GET {$path}, got {$response->status()}",
            );
        }
    }

    public function test_inertia_page_variants_are_not_cached_as_browser_documents(): void
    {
        $htmlResponse = $this->get('/luxury-bus-tour-dubai');

        $htmlResponse->assertOk();
        $this->assertNoStoreAppShellHeaders($htmlResponse);
        $htmlResponse->assertHeader('Pragma', 'no-cache');
        $htmlResponse->assertHeader('Expires', '0');

        $inertiaResponse = $this->withHeaders([
            'X-Inertia' => 'true',
            'X-Inertia-Version' => (string) filemtime(public_path('build/manifest.json')),
        ])->get('/luxury-bus-tour-dubai');

        $inertiaResponse->assertOk();
        $inertiaResponse->assertHeader('X-Inertia', 'true');
        $inertiaResponse->assertHeader('Vary', 'X-Inertia');
        $this->assertNoStoreAppShellHeaders($inertiaResponse);
        $inertiaResponse->assertHeader('Pragma', 'no-cache');
        $inertiaResponse->assertHeader('Expires', '0');
    }

    public function test_bus_tour_page_uses_admin_editable_content(): void
    {
        $page = BusTourPage::current();
        $page->update([
            'hero_title' => 'Editable Panoramic Bus Heading',
            'choice_media_image_path' => 'bus-tour-page/choice/admin-bus.jpg',
            'choice_media_video_path' => 'bus-tour-page/videos/admin-bus.mp4',
            'private_image_path' => 'bus-tour-page/private/admin-private-bus.jpg',
            'gallery_items' => [
                [
                    'title' => 'Uploaded bus media',
                    'copy' => 'Admin uploaded gallery files.',
                    'uploaded_images' => ['bus-tour-page/gallery/uploaded-gallery.jpg'],
                    'images' => ['https://example.com/fallback-gallery.jpg'],
                    'uploaded_videos' => ['bus-tour-page/gallery/videos/uploaded-gallery.mp4'],
                    'videos' => ['https://example.com/fallback-gallery.mp4'],
                ],
            ],
        ]);

        $response = $this->get('/luxury-bus-tour-dubai');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('BusTour')
            ->where('pageContent.hero.title', 'Editable Panoramic Bus Heading')
            ->where('pageContent.choice.mediaImageUrl', 'https://acutetourism.ae/uploads/bus-tour-page/choice/admin-bus.jpg')
            ->where('pageContent.choice.mediaVideoUrl', 'https://acutetourism.ae/uploads/bus-tour-page/videos/admin-bus.mp4')
            ->where('pageContent.privateSection.imageUrl', 'https://acutetourism.ae/uploads/bus-tour-page/private/admin-private-bus.jpg')
            ->where('pageContent.gallery.items.0.images.0', 'https://acutetourism.ae/uploads/bus-tour-page/gallery/uploaded-gallery.jpg')
            ->where('pageContent.gallery.items.0.images.1', 'https://example.com/fallback-gallery.jpg')
            ->where('pageContent.gallery.items.0.videos.0', 'https://acutetourism.ae/uploads/bus-tour-page/gallery/videos/uploaded-gallery.mp4')
            ->where('pageContent.gallery.items.0.videos.1', 'https://example.com/fallback-gallery.mp4')
        );
    }

    public function test_admin_can_save_bus_tour_page_with_gallery_upload_paths(): void
    {
        $admin = User::factory()->create(['is_admin' => true]);
        $page = BusTourPage::current();

        $page->update([
            'gallery_items' => [
                [
                    'title' => 'Legacy uploaded media',
                    'copy' => 'Existing gallery media.',
                    'image_uploads' => ['bus-tour-page/gallery/legacy-image.jpg'],
                    'video_uploads' => ['bus-tour-page/gallery/videos/legacy-video.mp4'],
                    'images' => [],
                    'videos' => [],
                ],
            ],
        ]);

        Livewire::actingAs($admin)
            ->test(EditBusTourPage::class, ['record' => $page->getKey()])
            ->fillForm([
                'gallery_items' => [
                    [
                        'title' => 'Saved uploaded media',
                        'copy' => 'Existing gallery media.',
                        'uploaded_images' => ['bus-tour-page/gallery/legacy-image.jpg'],
                        'uploaded_videos' => ['bus-tour-page/gallery/videos/legacy-video.mp4'],
                        'images' => [],
                        'videos' => [],
                    ],
                ],
            ])
            ->call('save')
            ->assertHasNoFormErrors();

        $page->refresh();

        $this->assertSame('Saved uploaded media', $page->gallery_items[0]['title']);
        $this->assertSame(['bus-tour-page/gallery/legacy-image.jpg'], $page->gallery_items[0]['uploaded_images']);
        $this->assertSame(['bus-tour-page/gallery/videos/legacy-video.mp4'], $page->gallery_items[0]['uploaded_videos']);
        $this->assertArrayNotHasKey('image_uploads', $page->gallery_items[0]);
        $this->assertArrayNotHasKey('video_uploads', $page->gallery_items[0]);
    }

    public function test_bus_tour_listings_are_admin_managed_visible_and_have_detail_pages(): void
    {
        BusTourListing::query()->create([
            'title' => 'Editable Admin Bus Route',
            'slug' => 'editable-admin-bus-route',
            'route_label' => 'Custom route',
            'price' => 'AED 123 per person',
            'panel_price' => 'AED 123 / person',
            'category_label' => 'Custom admin label',
            'short_description' => 'Custom admin package copy.',
            'tour_details' => 'Custom tour details copy.',
            'best_for' => 'Custom best-fit copy.',
            'card_image_url' => 'https://example.com/card.jpg',
            'detail_image_url' => 'https://example.com/detail.jpg',
            'tags' => ['Admin tag'],
            'highlights' => ['Admin highlight'],
            'included' => ['Admin inclusion'],
            'is_active' => true,
            'sort_order' => 1,
        ]);

        $landingResponse = $this->get('/luxury-bus-tour-dubai');

        $landingResponse->assertOk();
        $landingResponse->assertInertia(fn (Assert $page) => $page
            ->component('BusTour')
            ->where('pageContent.routesSection.items.0.title', 'Editable Admin Bus Route')
            ->where('pageContent.routesSection.items.0.price', 'AED 123 per person')
            ->where('pageContent.routesSection.items.0.cardImageUrl', 'https://example.com/card.jpg')
            ->where('site.primaryNavigation.3.children.0.label', 'All Panoramic Bus Tours')
            ->where('site.primaryNavigation.3.children.1.label', 'Editable Admin Bus Route')
            ->where('site.primaryNavigation.3.children.1.href', route('bus-tour.listings.show', 'editable-admin-bus-route'))
        );

        $detailResponse = $this->get('/luxury-bus-tour-dubai/editable-admin-bus-route');

        $detailResponse->assertOk();
        $detailResponse->assertInertia(fn (Assert $page) => $page
            ->component('BusTours/Show')
            ->where('listing.title', 'Editable Admin Bus Route')
            ->where('listing.tourDetails', 'Custom tour details copy.')
            ->where('listing.detailImageUrl', 'https://example.com/detail.jpg')
        );
    }

    public function test_static_pages_use_admin_editable_content(): void
    {
        StaticPage::query()->create([
            'page_key' => 'about',
            'admin_title' => 'About',
            'route_uri' => '/about',
            'seo_title' => 'Admin SEO About',
            'seo_description' => 'Admin SEO description for about page.',
            'hero_eyebrow' => 'Admin eyebrow',
            'hero_title' => 'Admin editable about heading',
            'hero_description' => 'Admin editable about copy.',
            'primary_cta_label' => 'Admin CTA',
            'primary_cta_url' => '/contact',
            'sidebar_label' => 'Admin sidebar label',
            'sidebar_title' => 'Admin sidebar title',
            'sidebar_items' => ['Admin sidebar item'],
            'metrics' => [
                ['value' => '99', 'label' => 'Admin metric'],
            ],
            'is_active' => true,
        ]);

        $response = $this->get('/about');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('About')
            ->where('seo.title', 'Admin SEO About')
            ->where('staticPage.hero.title', 'Admin editable about heading')
            ->where('staticPage.sidebar.items.0', 'Admin sidebar item')
            ->where('staticPage.metrics.0.value', '99')
        );
    }

    public function test_livewire_update_get_requests_redirect_to_admin(): void
    {
        $this->get('/livewire-f6ca27fd/update')
            ->assertRedirect('/admin');
    }

    public function test_tours_and_tickets_menu_uses_featured_admin_collections(): void
    {
        Collection::query()->update(['is_featured' => false]);

        Collection::query()->updateOrCreate(
            ['slug' => 'dubai'],
            [
                'name' => 'Dubai',
                'collection_group' => 'location',
                'summary' => 'Dubai tours and tickets.',
                'sort_order' => 10,
                'is_featured' => true,
            ],
        );

        Collection::query()->updateOrCreate(
            ['slug' => 'theme-parks'],
            [
                'name' => 'Theme Parks',
                'collection_group' => 'activity',
                'summary' => 'Theme park tickets.',
                'sort_order' => 20,
                'is_featured' => true,
            ],
        );

        Collection::query()->create([
            'name' => 'Unfeatured Test Collection',
            'slug' => 'unfeatured-test-collection',
            'collection_group' => 'activity',
            'summary' => 'Should not appear in the menu.',
            'sort_order' => 1,
            'is_featured' => false,
        ]);

        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('site.primaryNavigation.0.children.1.label', 'By Location')
            ->where('site.primaryNavigation.0.children.1.children.0.label', 'Dubai')
            ->where('site.primaryNavigation.0.children.1.children.0.href', route('experiences.location', 'dubai'))
            ->where('site.primaryNavigation.0.children.2.label', 'By Activity Type')
            ->where('site.primaryNavigation.0.children.2.children.0.label', 'Theme Parks')
            ->where('site.primaryNavigation.0.children.2.children.0.href', route('experiences.category', 'theme-parks'))
        );
    }

    public function test_tours_page_filters_use_featured_admin_collections(): void
    {
        Collection::query()->update(['is_featured' => false]);

        Collection::query()->create([
            'name' => 'Custom Location',
            'slug' => 'custom-location',
            'collection_group' => 'location',
            'summary' => 'Custom location tours.',
            'sort_order' => 10,
            'is_featured' => true,
        ]);

        Collection::query()->create([
            'name' => 'Custom Activity',
            'slug' => 'custom-activity',
            'collection_group' => 'activity',
            'summary' => 'Custom activity tours.',
            'sort_order' => 20,
            'is_featured' => true,
        ]);

        Collection::query()->create([
            'name' => 'Hidden Activity',
            'slug' => 'hidden-activity',
            'collection_group' => 'activity',
            'summary' => 'Should not appear in filters.',
            'sort_order' => 1,
            'is_featured' => false,
        ]);

        $response = $this->get('/dubai-tours-and-tickets');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->where('locationFilters.0.key', 'all')
            ->where('locationFilters.1.key', 'custom-location')
            ->where('locationFilters.1.label', 'Custom Location')
            ->where('typeFilters.0.key', 'all')
            ->where('typeFilters.1.key', 'custom-activity')
            ->where('typeFilters.1.label', 'Custom Activity')
            ->missing('typeFilters.2')
        );
    }

    protected function assertNoStoreAppShellHeaders($response): void
    {
        $cacheControl = (string) $response->headers->get('Cache-Control');

        foreach (['private', 'no-cache', 'no-store', 'must-revalidate'] as $directive) {
            $this->assertStringContainsString($directive, $cacheControl);
        }
    }
}
