<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inquiry received</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p>Hello {{ $inquiry->name }},</p>

    <p>Thank you for contacting Acute Tourism. We have received your request and our team will review it shortly.</p>

    <p>
        <strong>Interest:</strong> {{ $inquiry->interest }}<br>
        @if ($inquiry->experience_title)
            <strong>Experience:</strong> {{ $inquiry->experience_title }}<br>
        @endif
        @if ($inquiry->travel_date)
            <strong>Preferred travel date:</strong> {{ $inquiry->travel_date->format('F j, Y') }}<br>
        @endif
        @if ($inquiry->guest_count)
            <strong>Guests:</strong> {{ $inquiry->guest_count }}<br>
        @endif
    </p>

    <p><strong>Your message:</strong></p>
    <p style="white-space: pre-wrap;">{{ $inquiry->message }}</p>

    <p>If you need anything urgently, reply to this email or reach us on WhatsApp using the number on our website.</p>

    <p>Acute Tourism</p>
</body>
</html>
