<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>New inquiry</title>
</head>
<body style="font-family: Arial, sans-serif; color: #1f2933; line-height: 1.6;">
    <p><strong>New website inquiry</strong> ({{ $inquiry->source }})</p>

    <p>
        <strong>Name:</strong> {{ $inquiry->name }}<br>
        <strong>Email:</strong> {{ $inquiry->email }}<br>
        @if ($inquiry->phone)
            <strong>Phone:</strong> {{ $inquiry->phone }}<br>
        @endif
        <strong>Interest:</strong> {{ $inquiry->interest }}<br>
        @if ($inquiry->experience_title)
            <strong>Experience:</strong> {{ $inquiry->experience_title }}<br>
        @endif
        @if ($inquiry->travel_date)
            <strong>Travel date:</strong> {{ $inquiry->travel_date->format('F j, Y') }}<br>
        @endif
        @if ($inquiry->guest_count)
            <strong>Guests:</strong> {{ $inquiry->guest_count }}<br>
        @endif
        @if ($inquiry->source_url)
            <strong>Source URL:</strong> {{ $inquiry->source_url }}<br>
        @endif
    </p>

    <p><strong>Message / trip details (as submitted):</strong></p>
    <p style="white-space: pre-wrap; margin-top: 0.25rem; padding: 0.75rem 1rem; background: #f4f6f8; border-radius: 6px;">
        {{ $inquiry->message }}
    </p>

    <p>
        <a href="{{ url('/admin/experience-inquiries/'.$inquiry->id) }}">Open in admin</a>
    </p>
</body>
</html>
