<?php

namespace App\Models;

use App\Support\MediaUrl;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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
        'experience_type',
        'transfer_option',
        'booking_type',
        'price_from',
        'currency',
        'highlights',
        'inclusions',
        'exclusions',
        'important_notices',
        'expectation_steps',
        'best_for',
        'faqs',
        'cancellation_policy',
        'preferred_time_options',
        'preferred_language_options',
        'tour_options',
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
            'important_notices' => 'array',
            'expectation_steps' => 'array',
            'best_for' => 'array',
            'faqs' => 'array',
            'preferred_time_options' => 'array',
            'preferred_language_options' => 'array',
            'tour_options' => 'array',
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
