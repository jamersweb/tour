<?php

namespace App\Filament\Support;

use App\Support\MediaUrl;
use App\Support\UploadPath;
use Illuminate\Support\HtmlString;

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
            $state = collect($state)
                ->map(fn ($path) => UploadPath::normalize($path, preserveExternal: false) ?? $path)
                ->filter()
                ->values()
                ->all();

            return $state === [] ? $original : $state;
        }

        $state = UploadPath::normalize($state, preserveExternal: false) ?? $state;

        return filled($state) ? $state : $original;
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

    public static function applyRemovalControls(array $data, mixed $record = null): array
    {
        $removeHeroImage = (bool) ($data['remove_hero_image'] ?? false);
        unset($data['remove_hero_image']);

        $removeGalleryImages = collect($data['remove_gallery_images'] ?? [])
            ->map(fn ($path) => UploadPath::normalize($path, preserveExternal: false) ?? $path)
            ->filter()
            ->values()
            ->all();
        unset($data['remove_gallery_images']);

        if ($removeHeroImage) {
            $data['hero_image_path'] = null;
        }

        if ($removeGalleryImages !== []) {
            $galleryImages = array_key_exists('gallery_images', $data)
                ? self::formatState($data['gallery_images'])
                : self::formatState($record?->gallery_images ?? []);

            $data['gallery_images'] = collect($galleryImages)
                ->map(fn ($path) => UploadPath::normalize($path, preserveExternal: false) ?? $path)
                ->reject(fn ($path) => in_array($path, $removeGalleryImages, true))
                ->values()
                ->all();
        }

        return $data;
    }

    public static function galleryRemovalOptions(mixed $record): array
    {
        return collect($record?->gallery_images ?? [])
            ->mapWithKeys(fn ($path, int $index) => [
                $path => self::imageRemovalOptionLabel($path, $index + 1),
            ])
            ->all();
    }

    protected static function imageRemovalOptionLabel(mixed $path, int $position): HtmlString
    {
        $path = (string) $path;
        $url = MediaUrl::upload($path);
        $name = basename($path);

        return new HtmlString(
            '<span style="display:block;border:1px solid #3f3f46;border-radius:10px;overflow:hidden;background:#18181b;">'
            .'<img src="'.e($url).'" alt="" style="display:block;width:100%;height:96px;object-fit:cover;background:#0f0f11;">'
            .'<span style="display:block;padding:8px 10px;color:#f4f4f5;font-size:12px;font-weight:700;line-height:1.25;">'
            .'Delete image '.$position
            .'<span style="display:block;margin-top:3px;color:#a1a1aa;font-size:11px;font-weight:500;word-break:break-all;">'.e($name).'</span>'
            .'</span>'
            .'</span>',
        );
    }
}
