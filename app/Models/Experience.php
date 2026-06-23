<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\UploadPath;
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
        'important_notices',
        'expectation_steps',
        'best_for',
        'faqs',
        'cancellation_policy',
        'preferred_time_options',
        'preferred_language_options',
        'tour_options',
        'booking_options',
        'unavailable_dates',
        'unavailable_periods',
        'duration',
        'location',
        'pickup_note',
        'experience_type',
        'transfer_option',
        'booking_type',
        'price_from',
        'child_price_from',
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
            'child_price_from' => 'decimal:2',
            'gallery_images' => 'array',
            'gallery_videos' => 'array',
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
            'booking_options' => 'array',
            'unavailable_dates' => 'array',
            'unavailable_periods' => 'array',
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

    public function getHeroImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setHeroImagePathAttribute(mixed $value): void
    {
        $this->attributes['hero_image_path'] = UploadPath::normalize($value);
    }

    public function getGalleryImagesAttribute(mixed $value): array
    {
        return UploadPath::normalizeArray($value);
    }

    public function setGalleryImagesAttribute(mixed $value): void
    {
        $this->attributes['gallery_images'] = $value === null
            ? null
            : json_encode(UploadPath::normalizeArray($value));
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
            return MediaUrl::normalize($path);
        }

        return Storage::disk('uploads')->url($path);
    }
}
