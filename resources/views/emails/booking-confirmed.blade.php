<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Booking Confirmed</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p>Hello {{ $recipientName }},</p>

    <p>
        Your booking for <strong>{{ $transaction->bookingTitle() }}</strong>
        has been confirmed.
    </p>

    <p>
        Reference: <strong>{{ $transaction->reference }}</strong><br>
        @unless ($transaction->isCartCheckout())
            Travel date: <strong>{{ $transaction->travel_date?->format('F j, Y') ?? 'To be confirmed' }}</strong><br>
        @endunless
        Guests: <strong>{{ $transaction->guest_count ?? 1 }}</strong><br>
        Amount: <strong>{{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}</strong>
    </p>

    @if ($transaction->isCartCheckout())
        <table style="width:100%;border-collapse:collapse;margin:16px 0;">
            <thead>
                <tr>
                    <th align="left" style="border-bottom:1px solid #e5e7eb;padding:8px;">Item</th>
                    <th align="left" style="border-bottom:1px solid #e5e7eb;padding:8px;">Date</th>
                    <th align="left" style="border-bottom:1px solid #e5e7eb;padding:8px;">Guests</th>
                    <th align="right" style="border-bottom:1px solid #e5e7eb;padding:8px;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($transaction->cart_items as $item)
                    <tr>
                        <td style="border-bottom:1px solid #f1f5f9;padding:8px;">{{ $item['title'] ?? 'Cart item' }}</td>
                        <td style="border-bottom:1px solid #f1f5f9;padding:8px;">{{ $item['travelDate'] ?? 'To be confirmed' }}</td>
                        <td style="border-bottom:1px solid #f1f5f9;padding:8px;">{{ $item['guestCount'] ?? 1 }}</td>
                        <td align="right" style="border-bottom:1px solid #f1f5f9;padding:8px;">{{ $item['lineTotalFormatted'] ?? '' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

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
