<?php

namespace Tests\Feature;

use App\Models\Collection;
use App\Models\Experience;
use Database\Seeders\AcuteTourismSeeder;
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

    public function test_acute_tourism_seeder_preserves_admin_subcategory_edits(): void
    {
        $collection = Collection::query()->where('slug', 'luxury-desert-safaris')->firstOrFail();
        $collection->update([
            'name' => 'Admin Edited Desert',
            'summary' => 'Admin edited summary.',
            'sort_order' => 99,
            'is_featured' => false,
        ]);

        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();
        $customCollection = Collection::query()->where('slug', 'family-dubai')->firstOrFail();
        $experience->collections()->syncWithoutDetaching([
            $customCollection->id => ['sort_order' => 55],
        ]);

        $this->seed(AcuteTourismSeeder::class);

        $collection->refresh();
        $experience->refresh();

        $this->assertSame('Admin Edited Desert', $collection->name);
        $this->assertSame('Admin edited summary.', $collection->summary);
        $this->assertSame(99, $collection->sort_order);
        $this->assertFalse($collection->is_featured);
        $this->assertTrue($experience->collections()->whereKey($customCollection->id)->exists());
    }
}
