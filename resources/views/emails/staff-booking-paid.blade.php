<x-email.layout
    title="Payment Received"
    eyebrow="Staff notification"
    :preheader="'Payment received for '.$transaction->bookingTitle().' - '.$transaction->reference"
>
    <h1 style="margin:0 0 12px 0;color:#061a63;font-size:28px;line-height:1.18;font-weight:900;letter-spacing:-.02em;">
        Payment confirmed
    </h1>

    <p style="margin:0 0 16px 0;color:#34405f;font-size:15px;">
        A customer payment has been marked as <strong style="color:#17213f;">{{ $transaction->status }}</strong>.
    </p>

    <x-email.details title="Transaction details">
        <x-email.detail-row label="Reference" :value="$transaction->reference" />
        <x-email.detail-row label="Customer" :value="$transaction->customer_name.' ('.$transaction->customer_email.')'" />
        <x-email.detail-row label="Phone" :value="$transaction->customer_phone" />
        <x-email.detail-row label="Item" :value="$transaction->bookingTitle()" />
        <x-email.detail-row label="Amount" :value="$transaction->currency.' '.number_format((float) $transaction->amount, 2)" />
        <x-email.detail-row label="Travel date" :value="$transaction->travel_date?->format('F j, Y')" />
        <x-email.detail-row label="Guests" :value="$transaction->guest_count" />
        <x-email.detail-row label="Gateway payment ref" :value="$transaction->gateway_payment_ref" />
    </x-email.details>

    <x-email.cart-items :items="$transaction->cart_items" />
</x-email.layout>
