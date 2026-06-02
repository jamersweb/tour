<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartCheckoutRequest extends FormRequest
{
    protected function prepareForValidation(): void
    {
        $guestCount = (int) ($this->input('guest_count') ?: 1);
        $travelerContacts = $this->input('traveler_contacts');

        if (! is_array($travelerContacts) || $travelerContacts === []) {
            $guestCount = max(1, $guestCount);

            $this->merge([
                'guest_count' => $guestCount,
                'traveler_contacts' => collect(range(1, $guestCount))
                    ->map(fn () => [
                        'name' => (string) $this->input('name', ''),
                        'email' => (string) $this->input('email', ''),
                        'phone' => (string) $this->input('phone', ''),
                    ])
                    ->all(),
            ]);

            return;
        }

        if (! $this->filled('guest_count')) {
            $guestCount = count($travelerContacts);
        }

        $this->merge([
            'guest_count' => max(1, $guestCount),
        ]);
    }

    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email', 'max:160'],
            'phone' => ['required', 'string', 'max:40'],
            'travel_date' => ['nullable', 'date', 'after_or_equal:today'],
            'guest_count' => ['required', 'integer', 'min:1', 'max:100'],
            'booking_option' => ['nullable', 'string', 'max:160'],
            'tour_option' => ['nullable', 'string', 'max:120'],
            'preferred_time' => ['nullable', 'string', 'max:80'],
            'preferred_language' => ['nullable', 'string', 'max:80'],
            'hotel_pickup_location' => ['nullable', 'string', 'max:180'],
            'special_request' => ['nullable', 'string', 'max:1000'],
            'traveler_contacts' => ['required', 'array', 'min:1', 'max:100'],
            'traveler_contacts.*.name' => ['required', 'string', 'max:120'],
            'traveler_contacts.*.email' => ['required', 'email', 'max:160'],
            'traveler_contacts.*.phone' => ['required', 'string', 'max:40'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $guestCount = (int) $this->input('guest_count', 0);
            $travelerCount = count($this->input('traveler_contacts', []));

            if ($guestCount !== $travelerCount) {
                $validator->errors()->add(
                    'traveler_contacts',
                    'Traveler contact count must match the number of guests.'
                );
            }
        });
    }
}
