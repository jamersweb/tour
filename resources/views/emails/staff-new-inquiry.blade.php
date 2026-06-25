<x-email.layout
    title="New Inquiry"
    eyebrow="Staff notification"
    :preheader="'New website inquiry from '.$inquiry->name.' - '.$inquiry->interest"
>
    <h1 style="margin:0 0 12px 0;color:#061a63;font-size:28px;line-height:1.18;font-weight:900;letter-spacing:-.02em;">
        New website inquiry
    </h1>

    <p style="margin:0 0 16px 0;color:#34405f;font-size:15px;">
        A new lead was submitted from <strong style="color:#17213f;">{{ $inquiry->source }}</strong>.
    </p>

    <x-email.details title="Lead details">
        <x-email.detail-row label="Name" :value="$inquiry->name" />
        <x-email.detail-row label="Email" :value="$inquiry->email" />
        <x-email.detail-row label="Phone" :value="$inquiry->phone" />
        <x-email.detail-row label="Interest" :value="$inquiry->interest" />
        <x-email.detail-row label="Experience" :value="$inquiry->experience_title" />
        <x-email.detail-row label="Travel date" :value="$inquiry->travel_date?->format('F j, Y')" />
        <x-email.detail-row label="Guests" :value="$inquiry->guest_count" />
        <x-email.detail-row label="Source URL" :value="$inquiry->source_url" />
    </x-email.details>

    <x-email.note>
        <strong style="display:block;color:#061a63;margin-bottom:6px;">Message / trip details</strong>
        <span style="white-space:pre-wrap;">{{ $inquiry->message }}</span>
    </x-email.note>

    <x-email.button :href="url('/admin/experience-inquiries/'.$inquiry->id)">
        Open inquiry in admin
    </x-email.button>
</x-email.layout>
