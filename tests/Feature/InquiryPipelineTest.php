<?php

namespace Tests\Feature;

use App\Models\ExperienceInquiry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InquiryPipelineTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    public function test_open_pipeline_scope_excludes_won_and_lost_leads(): void
    {
        ExperienceInquiry::query()->create([
            'name' => 'Lead One',
            'email' => 'lead1@example.com',
            'interest' => 'Private Desert',
            'message' => 'Test',
            'source' => 'contact-page',
            'status' => 'new',
        ]);

        ExperienceInquiry::query()->create([
            'name' => 'Lead Two',
            'email' => 'lead2@example.com',
            'interest' => 'Yacht Experience',
            'message' => 'Test',
            'source' => 'contact-page',
            'status' => 'won',
        ]);

        ExperienceInquiry::query()->create([
            'name' => 'Lead Three',
            'email' => 'lead3@example.com',
            'interest' => 'General Planning',
            'message' => 'Test',
            'source' => 'contact-page',
            'status' => 'lost',
        ]);

        $this->assertSame(1, ExperienceInquiry::query()->openPipeline()->count());
    }

    public function test_overdue_follow_up_scope_only_returns_past_due_open_leads(): void
    {
        ExperienceInquiry::query()->create([
            'name' => 'Past Due',
            'email' => 'pastdue@example.com',
            'interest' => 'Private Desert',
            'message' => 'Test',
            'source' => 'contact-page',
            'status' => 'contacted',
            'follow_up_at' => now()->subDay(),
        ]);

        ExperienceInquiry::query()->create([
            'name' => 'Future Follow Up',
            'email' => 'future@example.com',
            'interest' => 'Private Desert',
            'message' => 'Test',
            'source' => 'contact-page',
            'status' => 'contacted',
            'follow_up_at' => now()->addDay(),
        ]);

        ExperienceInquiry::query()->create([
            'name' => 'Closed Lead',
            'email' => 'closed@example.com',
            'interest' => 'Private Desert',
            'message' => 'Test',
            'source' => 'contact-page',
            'status' => 'won',
            'follow_up_at' => now()->subDay(),
        ]);

        $this->assertSame(1, ExperienceInquiry::query()->overdueFollowUp()->count());
    }
}
