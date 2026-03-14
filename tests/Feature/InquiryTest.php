<?php

namespace Tests\Feature;

use App\Models\Experience;
use App\Models\ExperienceInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_contact_inquiry_can_be_submitted(): void
    {
        $response = $this->post('/inquiries', [
            'name' => 'Areeb Khan',
            'email' => 'areeb@example.com',
            'phone' => '+971500000000',
            'travel_date' => now()->addWeek()->toDateString(),
            'guest_count' => 4,
            'interest' => 'Private Desert',
            'message' => 'We want a private desert itinerary with pickup from Downtown Dubai.',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('success');

        $this->assertDatabaseHas((new ExperienceInquiry)->getTable(), [
            'name' => 'Areeb Khan',
            'email' => 'areeb@example.com',
            'interest' => 'Private Desert',
            'source' => 'contact-page',
            'status' => 'new',
        ]);
    }

    public function test_experience_inquiry_can_be_submitted_with_attached_context(): void
    {
        $experience = Experience::query()->where('slug', 'private-heritage-desert-safari')->firstOrFail();

        $response = $this->from("/experiences/{$experience->slug}")->post('/inquiries', [
            'experience_slug' => $experience->slug,
            'name' => 'Sana Malik',
            'email' => 'sana@example.com',
            'phone' => '+971511111111',
            'travel_date' => now()->addDays(10)->toDateString(),
            'guest_count' => 2,
            'interest' => 'Private Desert',
            'message' => 'Please share timing, private setup details, and pickup options.',
        ]);

        $response->assertRedirect("/experiences/{$experience->slug}");
        $response->assertSessionHas('success');

        $this->assertDatabaseHas((new ExperienceInquiry)->getTable(), [
            'name' => 'Sana Malik',
            'email' => 'sana@example.com',
            'interest' => 'Private Desert',
            'experience_id' => $experience->id,
            'experience_title' => $experience->title,
            'source' => 'experience-page',
            'source_url' => url("/experiences/{$experience->slug}"),
            'status' => 'new',
        ]);
    }
}
