<?php

namespace App\Filament\Support;

use App\Support\UploadPath;

class MediaUpload
{
    public static function formatState(mixed $state): mixed
    {
        if (is_array($state)) {
            return UploadPath::normalizeArray($state, preserveExternal: false);
        }

        return UploadPath::normalize($state, preserveExternal: false);
    }

    public static function dehydrateState(mixed $state, mixed $original): mixed
    {
        if (is_array($state)) {
            return collect($state)
                ->map(fn ($path) => UploadPath::normalize($path, preserveExternal: false) ?? $path)
                ->filter()
                ->values()
                ->all();
        }

        $state = UploadPath::normalize($state, preserveExternal: false) ?? $state;

        return filled($state) ? $state : null;
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
