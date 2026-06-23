<?php

namespace App\Filament\Support;

class MediaUpload
{
    public static function formatState(mixed $state): mixed
    {
        if (is_array($state)) {
            return collect($state)
                ->map(fn ($path) => self::toUploadDiskPath($path))
                ->filter()
                ->values()
                ->all();
        }

        return self::toUploadDiskPath($state);
    }

    public static function dehydrateState(mixed $state, mixed $original): mixed
    {
        if (is_array($state)) {
            $state = collect($state)
                ->map(fn ($path) => self::toUploadDiskPath($path) ?? $path)
                ->filter()
                ->values()
                ->all();

            return $state === [] ? $original : $state;
        }

        $state = self::toUploadDiskPath($state) ?? $state;

        return filled($state) ? $state : $original;
    }

    private static function toUploadDiskPath(mixed $path): ?string
    {
        if (! is_string($path) || trim($path) === '') {
            return null;
        }

        $path = trim($path);
        $path = str_replace('\\/', '/', $path);
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

        return null;
    }

    public static function normalizeData(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (! array_key_exists($field, $data)) {
                continue;
            }

            $data[$field] = self::formatState($data[$field]);
        }

        return $data;
    }
}
