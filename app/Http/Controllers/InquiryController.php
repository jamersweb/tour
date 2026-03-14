<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInquiryRequest;
use App\Models\Experience;
use App\Models\ExperienceInquiry;
use Illuminate\Http\RedirectResponse;

class InquiryController extends Controller
{
    public function store(StoreInquiryRequest $request): RedirectResponse
    {
        $payload = $request->validated();
        $experience = null;

        if (! empty($payload['experience_slug'])) {
            $experience = Experience::query()
                ->where('slug', $payload['experience_slug'])
                ->where('is_active', true)
                ->first();
        }

        unset($payload['experience_slug']);

        ExperienceInquiry::create($payload + [
            'experience_id' => $experience?->id,
            'user_id' => $request->user()?->id,
            'experience_title' => $experience?->title,
            'source' => $experience ? 'experience-page' : 'contact-page',
            'source_url' => url()->previous(),
            'status' => 'new',
        ]);

        return back()->with('success', $experience
            ? 'Experience inquiry received. The lead is now attached to this experience in the admin pipeline.'
            : 'Inquiry received. Acute Tourism can follow up from this new lead pipeline.');
    }
}
