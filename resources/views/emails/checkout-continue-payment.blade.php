<x-email.layout
    title="Complete Payment"
    eyebrow="Secure payment"
    :preheader="'Complete payment for '.$transaction->bookingTitle().' with Acute Tourism'"
>
    <h1 style="margin:0 0 12px 0;color:#061a63;font-size:28px;line-height:1.18;font-weight:900;letter-spacing:-.02em;">
        Complete your payment
    </h1>

    <p style="margin:0 0 16px 0;color:#34405f;font-size:15px;">
        Hello {{ $transaction->customer_name }}, you started checkout for <strong style="color:#17213f;">{{ $transaction->bookingTitle() }}</strong>.
        Complete your secure card payment using the link below.
    </p>

    <x-email.details title="Payment summary">
        <x-email.detail-row label="Reference" :value="$transaction->reference" />
        <x-email.detail-row label="Amount" :value="$transaction->currency.' '.number_format((float) $transaction->amount, 2)" />
    </x-email.details>

    <x-email.cart-items :items="$transaction->cart_items" />

    <x-email.button :href="$transaction->payment_url">
        Continue to payment
    </x-email.button>

    <p style="margin:12px 0 0 0;color:#6c7286;font-size:13px;line-height:1.6;">
        If the button does not work, copy this URL into your browser:<br>
        <span style="word-break:break-all;color:#061a63;">{{ $transaction->payment_url }}</span>
    </p>
</x-email.layout>
