<?php

namespace App\Services;

use App\Models\ExperienceInquiry;
use App\Models\ExperienceInquiryLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class ExperienceInquiryLogger
{
    /**
     * @param  array<string, mixed>  $properties
     */
    public function record(
        ExperienceInquiry $inquiry,
        string $action,
        ?string $description = null,
        array $properties = [],
        ?User $causer = null,
    ): void {
        try {
            ExperienceInquiryLog::query()->create([
                'experience_inquiry_id' => $inquiry->id,
                'user_id' => $causer?->id,
                'action' => $action,
                'description' => $description,
                'properties' => $properties === [] ? null : $properties,
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            Log::warning('experience_inquiry_log_failed', [
                'inquiry_id' => $inquiry->id,
                'action' => $action,
                'message' => $e->getMessage(),
            ]);
        }
    }
}
