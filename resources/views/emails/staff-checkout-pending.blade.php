<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout pending</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p><strong>Checkout started - payment not completed yet</strong></p>

    <p>
        <strong>Reference:</strong> {{ $transaction->reference }}<br>
        <strong>Customer:</strong> {{ $transaction->customer_name }} ({{ $transaction->customer_email }})<br>
        @if ($transaction->customer_phone)
            <strong>Phone:</strong> {{ $transaction->customer_phone }}<br>
        @endif
        <strong>Item:</strong> {{ $transaction->bookingTitle() }}<br>
        <strong>Amount:</strong> {{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}<br>
        @if ($transaction->travel_date)
            <strong>Travel date:</strong> {{ $transaction->travel_date->format('F j, Y') }}<br>
        @endif
        @if ($transaction->guest_count)
            <strong>Guests:</strong> {{ $transaction->guest_count }}<br>
        @endif
        <strong>Gateway order:</strong> {{ $transaction->gateway_order_ref }}<br>
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
        <a href="{{ url('/admin/payment-transactions/'.$transaction->id) }}">Open transaction in admin</a>
    </p>
</body>
</html>
