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
}
