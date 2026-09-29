<?php

namespace Tests\Feature;

use App\Filament\Resources\SiteSettings\Pages\EditSiteSetting;
use App\Models\SiteSetting;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class SiteSettingTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public static function logoValues(): array
    {
        return [
            'existing local logos' => ['/images/acute-tourism-logo.svg', '/images/acute-tourism-logo.png'],
            'external logos' => ['https://example.com/header.png', 'https://example.com/footer.svg'],
            'optional empty logos' => [null, null],
        ];
    }

    #[DataProvider('logoValues')]
    public function test_admin_can_update_contact_with_existing_logos(?string $header, ?string $footer): void
    {
        $settings = SiteSetting::current();
        $settings->update(['logo_url' => $header, 'footer_logo_url' => $footer]);

        Livewire::actingAs(User::factory()->create(['is_admin' => true]))
            ->test(EditSiteSetting::class, ['record' => $settings->getKey()])
            ->assertFormFieldExists('logo_url', fn (TextInput $field) => $field->getType() === 'text')
            ->assertFormFieldExists('footer_logo_url', fn (TextInput $field) => $field->getType() === 'text')
            ->fillForm(['contact_phone_secondary' => '03473639710'])
            ->call('save')
            ->assertHasNoFormErrors();

        $settings->refresh();
        $this->assertSame('03473639710', $settings->contact_phone_secondary);
        $this->assertSame($header, $settings->logo_url);
        $this->assertSame($footer, $settings->footer_logo_url);
    }

    public function test_admin_cannot_save_invalid_logo_values(): void
    {
        Livewire::actingAs(User::factory()->create(['is_admin' => true]))
            ->test(EditSiteSetting::class, ['record' => SiteSetting::current()->getKey()])
            ->fillForm(['logo_url' => 'not a url', 'footer_logo_url' => 'javascript:alert(1)'])
            ->call('save')
            ->assertHasFormErrors(['logo_url', 'footer_logo_url']);
    }

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
