<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StartCheckoutRequest extends FormRequest
{
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
