<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Mail\InquirySubmittedMail;
use App\Models\Experience;
use App\Models\ExperienceInquiry;
use App\Services\AdminBookingNotifier;
use App\Services\ExperienceInquiryLogger;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $experience = null;
        $source = $payload['source'] ?? null;

        if (! empty($payload['experience_slug'])) {
            $experience = Experience::query()
                ->where('slug', $payload['experience_slug'])
                ->where('is_active', true)
                ->first();
        }

        unset($payload['experience_slug'], $payload['source']);

        $inquiry = ExperienceInquiry::create($payload + [
            'experience_id' => $experience?->id,
            'user_id' => $request->user()?->id,
            'experience_title' => $experience?->title,
            'source' => $source ?: ($experience ? 'experience-page' : 'contact-page'),
            'source_url' => url()->previous(),
            'status' => 'new',
        ]);

        app(ExperienceInquiryLogger::class)->record(
            $inquiry,
            'inquiry_submitted',
            'Lead submitted from the website.',
            [
                'source' => $inquiry->source,
                'experience_slug' => $experience?->slug,
                'guest_count' => $inquiry->guest_count,
            ],
            $request->user(),
        );

        try {
            Mail::to($inquiry->email)->send(new InquirySubmittedMail($inquiry));
        } catch (\Throwable $exception) {
            Log::warning('Inquiry confirmation email to customer failed.', [
                'inquiry_id' => $inquiry->id,
                'message' => $exception->getMessage(),
            ]);
        }

        try {
            app(AdminBookingNotifier::class)->inquiryCreated($inquiry);
        } catch (\Throwable $exception) {
            Log::warning('Inquiry admin notification failed.', [
                'inquiry_id' => $inquiry->id,
                'message' => $exception->getMessage(),
            ]);
        }

        $message = match (true) {
            (bool) $experience => 'Experience inquiry received. The lead is now attached to this experience in the admin pipeline.',
            $source === 'visa-landing-page' => 'Thank you. Your visa inquiry was received. Our team is notified by email—you should also get a confirmation message shortly.',
            default => 'Inquiry received. Acute Tourism can follow up from this new lead pipeline.',
        };

        return back()->with('success', $message);
    }
}
