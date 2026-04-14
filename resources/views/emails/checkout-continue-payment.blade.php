<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Complete payment</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p>Hello {{ $transaction->customer_name }},</p>

    <p>
        You started checkout for <strong>{{ $transaction->payable?->title ?? 'your booking' }}</strong>.
        Complete your secure card payment using the link below (reference <strong>{{ $transaction->reference }}</strong>).
    </p>

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
