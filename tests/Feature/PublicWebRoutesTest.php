<?php

namespace Tests\Feature;

use App\Models\Article;
use App\Models\Collection;
use App\Models\Experience;
use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
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
}
