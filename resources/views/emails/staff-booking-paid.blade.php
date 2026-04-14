<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p><strong>Payment confirmed</strong> — {{ $transaction->status }}</p>

    <p>
        <strong>Reference:</strong> {{ $transaction->reference }}<br>
        <strong>Customer:</strong> {{ $transaction->customer_name }} ({{ $transaction->customer_email }})<br>
        @if ($transaction->customer_phone)
            <strong>Phone:</strong> {{ $transaction->customer_phone }}<br>
        @endif
        <strong>Item:</strong> {{ $transaction->payable?->title ?? class_basename($transaction->payable_type) }}<br>
        <strong>Amount:</strong> {{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}<br>
        @if ($transaction->travel_date)
            <strong>Travel date:</strong> {{ $transaction->travel_date->format('F j, Y') }}<br>
        @endif
        @if ($transaction->guest_count)
            <strong>Guests:</strong> {{ $transaction->guest_count }}<br>
        @endif
        @if ($transaction->gateway_payment_ref)
            <strong>Gateway payment ref:</strong> {{ $transaction->gateway_payment_ref }}<br>
        @endif
    </p>

    <p>
        <a href="{{ url('/admin/payment-transactions/'.$transaction->id) }}">Open transaction in admin</a>
    </p>
</body>
</html>
