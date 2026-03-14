<?php

namespace Tests\Feature;

use App\Models\Package;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class PackageTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_package_is_seeded(): void
    {
        $package = Package::query()->where('slug', 'ufc-fight-night-returns-to-abu-dhabi')->first();

        $this->assertNotNull($package);
        $this->assertSame('UFC Fight Night Returns to Abu Dhabi', $package->title);
        $this->assertNotEmpty($package->description);
        $this->assertNotEmpty($package->hero_image_url);
    }

    public function test_package_index_renders(): void
    {
        $response = $this->get('/packages');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Packages/Index')
            ->where('seo.title', 'Packages')
            ->has('packages')
        );
    }

    public function test_package_detail_renders(): void
    {
        $package = Package::query()->where('slug', 'ufc-fight-night-returns-to-abu-dhabi')->firstOrFail();

        $response = $this->get("/packages/{$package->slug}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Packages/Show')
            ->where('packageItem.title', $package->title)
            ->has('packageItem.itinerary')
        );
    }

    public function test_legacy_singular_package_url_redirects(): void
    {
        $response = $this->get('/package/ufc-fight-night-returns-to-abu-dhabi');

        $response->assertRedirect('/packages/ufc-fight-night-returns-to-abu-dhabi');
        $response->assertStatus(301);
    }
}
