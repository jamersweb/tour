<x-email.layout
    title="Booking Confirmed"
    eyebrow="Booking confirmed"
    :preheader="'Your Acute Tourism booking is confirmed: '.$transaction->bookingTitle()"
>
    <h1 style="margin:0 0 12px 0;color:#061a63;font-size:28px;line-height:1.18;font-weight:900;letter-spacing:-.02em;">
        Your booking is confirmed
    </h1>

    <p style="margin:0 0 16px 0;color:#34405f;font-size:15px;">
        Hello {{ $recipientName }}, your booking for <strong style="color:#17213f;">{{ $transaction->bookingTitle() }}</strong> has been confirmed.
    </p>

    <x-email.details title="Booking details">
        <x-email.detail-row label="Reference" :value="$transaction->reference" />
        @unless ($transaction->isCartCheckout())
            <x-email.detail-row label="Travel date" :value="$transaction->travel_date?->format('F j, Y') ?? 'To be confirmed'" />
        @endunless
        <x-email.detail-row label="Guests" :value="$transaction->guest_count ?? 1" />
        <x-email.detail-row label="Amount" :value="$transaction->currency.' '.number_format((float) $transaction->amount, 2)" />
    </x-email.details>

    <x-email.cart-items :items="$transaction->cart_items" />

    @if ($traveler)
        <x-email.note tone="gold">
            This confirmation was issued for traveler: <strong>{{ $traveler->name }}</strong>.
        </x-email.note>
    @endif

    <p style="margin:18px 0 0 0;color:#34405f;font-size:15px;">
        Our team will follow up shortly with logistics, pickup details, and any final coordination needed for your trip.
    </p>
</x-email.layout>
