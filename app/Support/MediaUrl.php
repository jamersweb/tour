<?php

namespace App\Support;

class MediaUrl
{
    public static function normalize(?string $url): ?string
    {
        if (! $url) {
            return null;
        }

        $url = trim($url);
        $parts = parse_url($url);

        if (! is_array($parts)) {
            return $url;
        }

        $host = strtolower($parts['host'] ?? '');
        $path = $parts['path'] ?? '';

        if (in_array($host, ['acutetourism.org', 'www.acutetourism.org'], true)
            && str_starts_with($path, '/uploads/')) {
            return '/legacy-media'.$path;
        }

        return $url;
    }
}
