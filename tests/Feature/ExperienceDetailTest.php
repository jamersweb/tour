<?php

namespace Tests\Feature;

use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Tests\TestCase;

class ExperienceDetailTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_public_experience_detail_page_renders(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $response = $this->get("/experiences/{$experience->slug}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Experiences/Show')
            ->where('seo.title', $experience->seo_title)
            ->where('experience.title', $experience->title)
            ->where('experience.slug', $experience->slug)
            ->has('experience.inclusions')
            ->has('experience.galleryImageUrls')
            ->has('relatedExperiences')
        );
    }

    public function test_experience_detail_uses_admin_product_page_sections(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();
        $experience->update([
            'important_notices' => ['Bring original ID for access.'],
            'expectation_steps' => [
                ['label' => 'Private pickup', 'copy' => 'Driver meets you at your hotel lobby.'],
            ],
            'best_for' => ['Small private groups'],
            'faqs' => [
                ['question' => 'Can pickup be private?', 'answer' => 'Yes, private transfer can be arranged.'],
            ],
            'cancellation_policy' => 'Custom cancellation policy from admin.',
        ]);

        $response = $this->get("/experiences/{$experience->slug}");

        $response->assertOk();
        $response->assertInertia(fn (Assert $page) => $page
            ->component('Experiences/Show')
            ->where('experience.importantNotices.0', 'Bring original ID for access.')
            ->where('experience.expectationSteps.0.label', 'Private pickup')
            ->where('experience.bestFor.0', 'Small private groups')
            ->where('experience.faqs.0.question', 'Can pickup be private?')
            ->where('experience.cancellationPolicy', 'Custom cancellation policy from admin.')
        );
    }
}
