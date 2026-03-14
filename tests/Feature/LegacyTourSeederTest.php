<?php

namespace Tests\Feature;

use App\Models\Experience;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LegacyTourSeederTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_legacy_tours_are_imported_into_experiences(): void
    {
        $experience = Experience::query()->where('slug', 'dinner-cruise-with-acute')->first();

        $this->assertNotNull($experience);
        $this->assertSame('Dinner Cruise with Acute', $experience->title);
        $this->assertNotEmpty($experience->description);
        $this->assertNotEmpty($experience->hero_image_url);
    }
}
