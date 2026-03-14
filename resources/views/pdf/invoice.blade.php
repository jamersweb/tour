<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Invoice {{ $transaction->reference }}</title>
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #1f2933; font-size: 13px; }
        .header, .section { margin-bottom: 24px; }
        .brand { font-size: 22px; font-weight: bold; color: #8b6b2f; }
        .muted { color: #66788a; }
        .panel { border: 1px solid #d9d7d2; border-radius: 8px; padding: 16px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid td, .grid th { padding: 8px 0; vertical-align: top; text-align: left; }
        .grid th { font-size: 11px; text-transform: uppercase; color: #66788a; border-bottom: 1px solid #e5e7eb; }
        .grid td { border-bottom: 1px solid #f0f2f4; }
        .totals { width: 100%; max-width: 280px; margin-left: auto; }
        .totals td { padding: 6px 0; }
        .right { text-align: right; }
    </style>
</head>
<body>
    <div class="header">
        <div class="brand">Acute Tourism</div>
        <div class="muted">Invoice / Receipt</div>
    </div>

    <div class="section panel">
        <table class="grid">
            <tr>
                <th>Reference</th>
                <th>Item</th>
                <th>Status</th>
                <th>Travel Date</th>
            </tr>
            <tr>
                <td>{{ $transaction->reference }}</td>
                <td>{{ $transaction->payable?->title ?? 'Booking' }}</td>
                <td>{{ ucfirst($transaction->status) }}</td>
                <td>{{ $transaction->travel_date?->format('F j, Y') ?? 'To be confirmed' }}</td>
            </tr>
        </table>
    </div>

    <div class="section panel">
        <table class="grid">
            <tr>
                <th>Booked By</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Guests</th>
            </tr>
            <tr>
                <td>{{ $transaction->customer_name }}</td>
                <td>{{ $transaction->customer_email }}</td>
                <td>{{ $transaction->customer_phone ?: 'Not provided' }}</td>
                <td>{{ $transaction->guest_count ?? max($transaction->travelers->count(), 1) }}</td>
            </tr>
        </table>
    </div>

    @if ($transaction->travelers->isNotEmpty())
        <div class="section panel">
            <table class="grid">
                <tr>
                    <th>#</th>
                    <th>Traveler</th>
                    <th>Email</th>
                    <th>Phone</th>
                </tr>
                @foreach ($transaction->travelers as $traveler)
                    <tr>
                        <td>{{ $traveler->position }}</td>
                        <td>{{ $traveler->name }}</td>
                        <td>{{ $traveler->email }}</td>
                        <td>{{ $traveler->phone ?: 'Not provided' }}</td>
                    </tr>
                @endforeach
            </table>
        </div>
    @endif

    <div class="section">
        <table class="totals">
            <tr>
                <td>Subtotal</td>
                <td class="right">{{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}</td>
            </tr>
            <tr>
                <td><strong>Total</strong></td>
                <td class="right"><strong>{{ $transaction->currency }} {{ number_format((float) $transaction->amount, 2) }}</strong></td>
            </tr>
        </table>
    </div>

    <div class="section panel">
        <table class="grid">
            <tr>
                <th>Paid At</th>
                <th>Confirmation Sent</th>
                <th>Reconciled</th>
                <th>Refunded</th>
            </tr>
            <tr>
                <td>{{ $transaction->paid_at?->format('F j, Y g:i A') ?? 'Pending' }}</td>
                <td>{{ $transaction->confirmation_sent_at?->format('F j, Y g:i A') ?? 'Pending' }}</td>
                <td>
                    {{ $transaction->reconciled_at?->format('F j, Y g:i A') ?? 'Not reconciled' }}
                    @if ($transaction->reconciledBy)
                        <br><span class="muted">by {{ $transaction->reconciledBy->name }}</span>
                    @endif
                </td>
                <td>
                    {{ $transaction->refunded_at?->format('F j, Y g:i A') ?? 'Not refunded' }}
                    @if ($transaction->refundedBy)
                        <br><span class="muted">by {{ $transaction->refundedBy->name }}</span>
                    @endif
                </td>
            </tr>
        </table>
    </div>

    @if ($transaction->notes)
        <div class="section panel">
            <strong>Notes</strong>
            <p>{{ $transaction->notes }}</p>
        </div>
    @endif
</body>
</html>
