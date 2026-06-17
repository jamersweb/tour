<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Inertia\Testing\AssertableInertia as Assert;
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
            'name' => 'Hidden Test Collection',
            'slug' => 'hidden-test-collection',
            'collection_group' => 'activity',
            'summary' => 'Should not appear in the menu.',
            'sort_order' => 1,
            'is_featured' => true,
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

    protected function assertNoStoreAppShellHeaders($response): void
    {
        $cacheControl = (string) $response->headers->get('Cache-Control');

        foreach (['private', 'no-cache', 'no-store', 'must-revalidate'] as $directive) {
            $this->assertStringContainsString($directive, $cacheControl);
        }
    }
}
