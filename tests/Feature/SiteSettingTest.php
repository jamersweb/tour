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
        $settings = SiteSetting::current();

        $response = $this->get('/');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('hero.eyebrow', 'Custom Travel Planning in Dubai')
            ->where('hero.title', 'Travel Planned Around You')
            ->where('homeSections.collectionsEyebrow', $settings->home_collections_eyebrow)
            ->where('homeSections.collectionsTitle', $settings->home_collections_title)
            ->where('site.organization.type', 'TravelAgency')
            ->where('site.organization.legalName', 'Acute Tourism LLC')
        );
    }

    public function test_public_shell_has_default_seo_and_security_headers(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('<title inertia>Acute Tourism | Dubai Tours, Holiday Packages &amp; Visa Assistance</title>', false);
        $response->assertSee('<meta name="description" content="Book Dubai tours, holiday packages, attraction tickets, panoramic bus experiences, and outbound visa assistance with Acute Tourism in the UAE.">', false);
        $response->assertHeader('X-Content-Type-Options', 'nosniff');
        $response->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->assertHeader('Content-Security-Policy', 'upgrade-insecure-requests');
    }
}
