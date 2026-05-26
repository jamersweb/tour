<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreInquiryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'experience_slug' => ['nullable', 'string', 'exists:experiences,slug'],
            'source' => ['nullable', 'string', 'max:40'],
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['nullable', 'string', 'max:40'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'guest_count' => ['nullable', 'integer', 'min:1', 'max:100'],
            'interest' => ['required', 'string', 'max:120'],
            'message' => ['required', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Please enter your email address.',
            'email.email' => 'Please enter a valid email address (for example name@example.com).',
        ];
    }
}
