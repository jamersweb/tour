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
        $this->assertStringContainsString('<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $content);
        $this->assertStringContainsString(url('/pages-sitemap.xml'), $content);
        $this->assertStringContainsString(url('/experiences-sitemap.xml'), $content);
        $this->assertStringContainsString(url('/packages-sitemap.xml'), $content);
        $this->assertStringContainsString(url('/blog-sitemap.xml'), $content);

        $blogResponse = $this->get('/blog-sitemap.xml');

        $blogResponse->assertOk();
        $blogContent = $blogResponse->getContent();
        $this->assertStringContainsString('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">', $blogContent);
        $this->assertStringContainsString(url('/blog'), $blogContent);

        $experienceResponse = $this->get('/experiences-sitemap.xml');

        $experienceResponse->assertOk();
        $this->assertStringContainsString(url('/dubai-tours-and-tickets'), $experienceResponse->getContent());
        $this->assertStringNotContainsString(url('/experiences').'</loc>', $experienceResponse->getContent());

        $packageResponse = $this->get('/packages-sitemap.xml');

        $packageResponse->assertOk();
        $this->assertStringContainsString(url('/dubai-holiday-packages'), $packageResponse->getContent());
        $this->assertStringNotContainsString(url('/packages').'</loc>', $packageResponse->getContent());
    }
}
