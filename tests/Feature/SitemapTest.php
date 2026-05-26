<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SitemapTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_sitemap_xml_is_valid_and_lists_core_urls(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('Content-Type', 'application/xml; charset=UTF-8');

        $content = $response->getContent();
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString(url('/experiences/private-heritage-desert-safari'), $content);
        $this->assertStringContainsString(url('/packages/ufc-fight-night-returns-to-abu-dhabi'), $content);
        $this->assertStringContainsString(url('/blog/private-yacht-charter-dubai-guide'), $content);
        $this->assertStringContainsString(url('/blog?category=yacht-guide'), $content);
    }
}
