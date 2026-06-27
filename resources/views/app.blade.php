<!DOCTYPE html>
<html lang="en">
    <head>
        @php
            $defaultTitle = 'Acute Tourism | Dubai Tours, Holiday Packages & Visa Assistance';
            $defaultDescription = 'Book Dubai tours, holiday packages, attraction tickets, panoramic bus experiences, and outbound visa assistance with Acute Tourism in the UAE.';
            $defaultUrl = url()->current();
            $defaultImage = url('/images/acute-tourism-logo.png');
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
        <script async src="https://www.googletagmanager.com/gtag/js?id=G-HC9SWHE1X2"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());
            gtag('config', 'G-HC9SWHE1X2');
        </script>
        @if (request()->routeIs('home'))
            <link
                rel="preload"
                as="image"
                href="https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=72"
                imagesrcset="https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=768&q=68 768w, https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1200&q=72 1200w, https://images.unsplash.com/photo-1518684079-3c830dcef090?auto=format&fit=crop&w=1600&q=72 1600w"
                imagesizes="(max-width: 768px) 100vw, 775px"
                fetchpriority="high"
            >
        @endif
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @inertiaHead
    </head>
    <body>
        @inertia
    </body>
</html>
