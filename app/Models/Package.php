<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Facades\Storage;

class Package extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
        'hero_image_path',
        'hero_video_url',
        'gallery_images',
        'gallery_videos',
        'duration',
        'days',
        'nights',
        'location',
        'group_size_min',
        'group_size_max',
        'price_from',
        'sale_price',
        'currency',
        'highlights',
        'inclusions',
        'exclusions',
        'important_notices',
        'best_for',
        'cancellation_policy',
        'itinerary',
        'is_featured',
        'is_active',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'gallery_videos' => 'array',
            'days' => 'integer',
            'nights' => 'integer',
            'group_size_min' => 'integer',
            'group_size_max' => 'integer',
            'price_from' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'highlights' => 'array',
            'inclusions' => 'array',
            'exclusions' => 'array',
            'important_notices' => 'array',
            'best_for' => 'array',
            'itinerary' => 'array',
            'is_featured' => 'boolean',
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
            return MediaUrl::normalize($path);
        }

        return Storage::disk('uploads')->url($path);
    }
}
