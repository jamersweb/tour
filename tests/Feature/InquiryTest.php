<?php

namespace Tests\Feature;

use App\Mail\InquirySubmittedMail;
use App\Mail\StaffNewInquiryMail;
use App\Models\Experience;
use App\Models\ExperienceInquiry;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class InquiryTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_contact_inquiry_can_be_submitted(): void
    {
        Mail::fake();

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

        Mail::assertSent(InquirySubmittedMail::class, 1);
        Mail::assertSent(StaffNewInquiryMail::class, 1);

        $this->assertSame(1, DB::table('experience_inquiry_logs')->count());
        $this->assertDatabaseHas('experience_inquiry_logs', [
            'action' => 'inquiry_submitted',
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();
        $this->assertSame(1, DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $admin->getKey())
            ->count());
    }

    public function test_experience_inquiry_can_be_submitted_with_attached_context(): void
    {
        Mail::fake();

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

        Mail::assertSent(InquirySubmittedMail::class, 1);
        Mail::assertSent(StaffNewInquiryMail::class, 1);

        $this->assertSame(1, DB::table('experience_inquiry_logs')->count());
        $this->assertDatabaseHas('experience_inquiry_logs', [
            'action' => 'inquiry_submitted',
        ]);

        $admin = User::query()->where('is_admin', true)->firstOrFail();
        $this->assertSame(1, DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $admin->getKey())
            ->count());
    }

    public function test_visa_landing_inquiry_records_source_and_emails_staff(): void
    {
        Mail::fake();

        $response = $this->from(route('visa.schengen'))->post('/inquiries', [
            'source' => 'visa-landing-page',
            'name' => 'Visa Lead',
            'email' => 'visa-lead@example.com',
            'phone' => '+971522222222',
            'travel_date' => now()->addMonth()->toDateString(),
            'guest_count' => 1,
            'interest' => 'Private Desert',
            'message' => 'Need help with Schengen visa from UAE.',
        ]);

        $response->assertRedirect(route('visa.schengen'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas((new ExperienceInquiry)->getTable(), [
            'name' => 'Visa Lead',
            'email' => 'visa-lead@example.com',
            'source' => 'visa-landing-page',
            'status' => 'new',
        ]);

        Mail::assertSent(InquirySubmittedMail::class, 1);
        Mail::assertSent(StaffNewInquiryMail::class, 1);

        $this->assertSame(1, DB::table('experience_inquiry_logs')->where('action', 'inquiry_submitted')->count());

        $admin = User::query()->where('is_admin', true)->firstOrFail();
        $this->assertSame(1, DB::table('notifications')
            ->where('notifiable_type', User::class)
            ->where('notifiable_id', $admin->getKey())
            ->count());
    }
}
