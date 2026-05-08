<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Tour extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'description',
        'hero_image_path',
        'hero_video_url',
        'gallery_images',
        'gallery_videos',
        'duration',
        'location',
        'pickup_note',
        'price_from',
        'currency',
        'highlights',
        'inclusions',
        'exclusions',
        'is_featured',
        'is_private',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'gallery_videos' => 'array',
            'price_from' => 'decimal:2',
            'highlights' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'is_featured' => 'boolean',
            'is_private' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        return $this->resolveMediaPath($this->hero_image_path);
    }

    public function getGalleryImageUrlsAttribute(): array
    {
        return collect($this->gallery_images ?? [])
            ->map(fn ($path) => $this->resolveMediaPath($path))
            ->filter()
            ->values()
            ->all();
    }

    public function getGalleryVideoUrlsAttribute(): array
    {
        return collect($this->gallery_videos ?? [])
            ->filter()
            ->values()
            ->all();
    }

    protected function resolveMediaPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }

        return Storage::disk('uploads')->url($path);
    }
}
