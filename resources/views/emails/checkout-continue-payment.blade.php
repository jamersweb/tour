<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete payment</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p>Hello {{ $transaction->customer_name }},</p>

    <p>
        You started checkout for <strong>{{ $transaction->bookingTitle() }}</strong>.
        Complete your secure card payment using the link below (reference <strong>{{ $transaction->reference }}</strong>).
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

    <p>
        <a href="{{ $transaction->payment_url }}" style="display:inline-block;padding:12px 20px;background:#0f172a;color:#fff;text-decoration:none;border-radius:6px;">Continue to payment</a>
    </p>

    <p style="font-size: 14px; color: #64748b;">
        If the button does not work, copy this URL into your browser:<br>
        {{ $transaction->payment_url }}
    </p>

    <p>Amount: <strong>{{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}</strong></p>

    <p>Acute Tourism</p>
</body>
</html>
