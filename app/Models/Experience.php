<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Experience extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'category',
        'short_description',
        'hero_summary',
        'description',
        'hero_image_path',
        'hero_video_url',
        'gallery_images',
        'gallery_videos',
        'highlights',
        'inclusions',
        'exclusions',
        'duration',
        'location',
        'pickup_note',
        'price_from',
        'currency',
        'is_featured',
        'show_on_homepage',
        'homepage_sort_order',
        'is_private',
        'is_active',
        'tag',
        'sort_order',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'price_from' => 'decimal:2',
            'gallery_images' => 'array',
            'gallery_videos' => 'array',
            'highlights' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'is_featured' => 'boolean',
            'show_on_homepage' => 'boolean',
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

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class)
            ->withPivot('sort_order')
            ->withTimestamps();
    }

    public function reviews(): MorphMany
    {
        return $this->morphMany(Review::class, 'reviewable');
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
