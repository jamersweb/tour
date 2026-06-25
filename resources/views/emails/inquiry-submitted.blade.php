<x-email.layout
    title="Inquiry Received"
    eyebrow="Request received"
    :preheader="'We received your Acute Tourism request about '.$inquiry->interest"
>
    <h1 style="margin:0 0 12px 0;color:#061a63;font-size:28px;line-height:1.18;font-weight:900;letter-spacing:-.02em;">
        We received your request
    </h1>

    <p style="margin:0 0 16px 0;color:#34405f;font-size:15px;">
        Hello {{ $inquiry->name }}, thank you for contacting Acute Tourism. Our team will review your request and respond shortly.
    </p>

    <x-email.details title="Request details">
        <x-email.detail-row label="Interest" :value="$inquiry->interest" />
        <x-email.detail-row label="Experience" :value="$inquiry->experience_title" />
        <x-email.detail-row label="Preferred travel date" :value="$inquiry->travel_date?->format('F j, Y')" />
        <x-email.detail-row label="Guests" :value="$inquiry->guest_count" />
    </x-email.details>

    <x-email.note>
        <strong style="display:block;color:#061a63;margin-bottom:6px;">Your message</strong>
        <span style="white-space:pre-wrap;">{{ $inquiry->message }}</span>
    </x-email.note>

    <p style="margin:18px 0 0 0;color:#34405f;font-size:15px;">
        If you need anything urgently, reply to this email or reach us on WhatsApp using the number on our website.
    </p>
</x-email.layout>
