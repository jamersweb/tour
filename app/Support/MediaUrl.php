<?php

namespace App\Support;

class MediaUrl
{
    private const LOCAL_LOGO = '/images/acute-tourism-logo.png';
    private const DEAD_LEGACY_LOGOS = [
        '/images/acute-tourism-logo.svg',
        '/legacy_media/uploads/0000/6/2025/03/19/5.png',
        '/legacy-media/uploads/0000/6/2025/03/19/5.png',
        '/legacy_media/uploads/0000/6/2025/03/14/4-2.png',
        '/legacy-media/uploads/0000/6/2025/03/14/4-2.png',
        'https://acutetourism.org/uploads/0000/6/2025/03/19/5.png',
        'https://acutetourism.org/uploads/0000/6/2025/03/14/4-2.png',
    ];

    public static function upload(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        return rtrim(config('filesystems.disks.uploads.url', 'https://acutetourism.ae/uploads'), '/')
            .'/'.ltrim($path, '/');
    }

    public static function normalize(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);

        if (in_array($url, self::DEAD_LEGACY_LOGOS, true)) {
            return self::LOCAL_LOGO;
        }

        if (str_starts_with($url, '/legacy_media/uploads/')) {
            return str_replace('/legacy_media/uploads/', '/legacy-media/uploads/', $url);
        }

        $parts = parse_url($url);

        if (! is_array($parts)) {
            return $url;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if ($host === 'new.acutetourism.org' && str_starts_with($path, '/uploads/')) {
            return 'https://acutetourism.ae'.$path;
        }

        if (in_array($host, ['acutetourism.org', 'www.acutetourism.org'], true)
            && str_starts_with($path, '/uploads/')) {
            return '/legacy-media'.$path;
        }

        return $url;
    }
}
