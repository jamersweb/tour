<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class FaqTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_faq_page_uses_database_groups(): void
    {
        $response = $this->get('/faq');

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Faq')
            ->where('seo.title', 'FAQ')
            ->has('featuredFaqs')
            ->has('faqGroups')
        );
    }
}
