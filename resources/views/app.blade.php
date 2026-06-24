<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $defaultTitle = 'Acute Tourism | Dubai Tours, Holiday Packages & Visa Assistance';
            $defaultDescription = 'Book Dubai tours, holiday packages, attraction tickets, panoramic bus experiences, and outbound visa assistance with Acute Tourism in the UAE.';
            $defaultUrl = url()->current();
            $defaultImage = url('/legacy-media/uploads/0000/6/2025/03/19/5.png');
        @endphp
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title inertia>{{ $defaultTitle }}</title>
        <meta name="description" content="{{ $defaultDescription }}">
        <link rel="canonical" href="{{ $defaultUrl }}">
        <meta property="og:type" content="website">
        <meta property="og:site_name" content="Acute Tourism">
        <meta property="og:title" content="{{ $defaultTitle }}">
        <meta property="og:description" content="{{ $defaultDescription }}">
        <meta property="og:url" content="{{ $defaultUrl }}">
        <meta property="og:image" content="{{ $defaultImage }}">
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:title" content="{{ $defaultTitle }}">
        <meta name="twitter:description" content="{{ $defaultDescription }}">
        <meta name="twitter:image" content="{{ $defaultImage }}">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
