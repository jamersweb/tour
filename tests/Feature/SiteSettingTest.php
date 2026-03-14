<?php

namespace Tests\Feature;

use App\Models\SiteSetting;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_site_setting_singleton_is_seeded(): void
    {
        $settings = SiteSetting::current();

        $this->assertSame('Acute Tourism', $settings->site_name);
        $this->assertContains('Network Payment Gateway', $settings->footer_build_notes);
    }

    public function test_home_page_uses_settings_driven_content(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('hero.eyebrow', 'Curated Dubai Experiences')
            ->where('homeSections.trustHeading', 'Rebuild Focus')
            ->where('site.organization.type', 'TravelAgency')
            ->where('site.organization.legalName', 'Acute Tourism LLC')
            ->where('trustPoints.3', 'Planned Network Payment Gateway integration for production checkout')
        );
    }
}
