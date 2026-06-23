<?php

namespace App\Support;

class UploadPath
{
    public static function normalize(mixed $path, bool $preserveExternal = true): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = str_replace('\\/', '/', trim($path));
        $parts = parse_url($path);

        if (! is_array($parts) || empty($parts['host'])) {
            return ltrim($path, '/');
        }

        $host = strtolower($parts['host']);
        $urlPath = $parts['path'] ?? '';

        if (in_array($host, ['new.acutetourism.org', 'acutetourism.ae', 'www.acutetourism.ae'], true)
            && str_starts_with($urlPath, '/uploads/')) {
            return ltrim(substr($urlPath, strlen('/uploads/')), '/');
        }

        return $preserveExternal ? $path : null;
    }

    public static function normalizeArray(mixed $paths, bool $preserveExternal = true): array
    {
        if (is_string($paths)) {
            $decoded = json_decode($paths, true);
            $paths = is_array($decoded) ? $decoded : [$paths];
        }

        if (! is_array($paths)) {
            return [];
        }

        return collect($paths)
            ->map(fn ($path) => self::normalize($path, $preserveExternal))
            ->filter()
            ->values()
            ->all();
    }
}
