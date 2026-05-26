<?php

namespace Tests\Feature;

use App\Models\Redirect;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RedirectTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_seeded_legacy_route_redirects_to_new_destination(): void
    {
        $response = $this->get('/tours');

        $response->assertRedirect('/experiences');
        $response->assertStatus(301);
    }

    public function test_redirect_match_metrics_are_updated(): void
    {
        $redirect = Redirect::query()->where('source_path', '/faqs')->firstOrFail();

        $this->assertSame(0, $redirect->match_hits);

        $this->get('/faqs')->assertRedirect('/faq');

        $redirect->refresh();

        $this->assertSame(1, $redirect->match_hits);
        $this->assertNotNull($redirect->last_matched_at);
    }

    public function test_redirect_middleware_ignores_post_requests(): void
    {
        $response = $this->post('/faqs');

        $response->assertStatus(404);
    }
}
