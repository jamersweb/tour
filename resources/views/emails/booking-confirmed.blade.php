<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p>Hello {{ $recipientName }},</p>

    <p>
        Your booking for <strong>{{ $transaction->payable?->title ?? 'Acute Tourism experience' }}</strong>
        has been confirmed.
    </p>

    <p>
        Reference: <strong>{{ $transaction->reference }}</strong><br>
        Travel date: <strong>{{ $transaction->travel_date?->format('F j, Y') ?? 'To be confirmed' }}</strong><br>
        Guests: <strong>{{ $transaction->guest_count ?? 1 }}</strong><br>
        Amount: <strong>{{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}</strong>
    </p>

    @if ($traveler)
        <p>
            This confirmation was issued for traveler: <strong>{{ $traveler->name }}</strong>.
        </p>
    @endif

    <p>
        Our team will follow up shortly with logistics, pickup details, and any final coordination needed for your trip.
    </p>

    <p>Acute Tourism</p>
</body>
</html>
