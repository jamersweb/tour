<?php

namespace App\Observers;

use App\Models\ExperienceInquiry;
use App\Services\ExperienceInquiryLogger;

class ExperienceInquiryObserver
{
    public function updated(ExperienceInquiry $inquiry): void
    {
        if (! $inquiry->wasChanged('status')) {
            return;
        }

        $from = $inquiry->getOriginal('status');
        $to = $inquiry->status;

        app(ExperienceInquiryLogger::class)->record(
            $inquiry,
            'status_changed',
            "Pipeline status changed from {$from} to {$to}.",
            [
                'from' => $from,
                'to' => $to,
            ],
            auth()->user(),
        );
    }
}
