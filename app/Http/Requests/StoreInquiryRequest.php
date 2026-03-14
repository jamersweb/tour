<?php

namespace App\Http\Requests;

use App\Models\SiteSetting;
use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $interestOptions = SiteSetting::current()->interest_options ?? [];

        return [
            'experience_slug' => ['nullable', 'string', 'exists:experiences,slug'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'interest' => ['required', 'string', 'max:120', ...($interestOptions ? ['in:'.implode(',', $interestOptions)] : [])],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }
}
