<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\UploadPath;
use Illuminate\Database\Eloquent\Model;

class BusTourListing extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'route_label',
        'price',
        'panel_price',
        'category_label',
        'short_description',
        'best_for',
        'card_image_path',
        'card_image_url',
        'detail_image_path',
        'detail_image_url',
        'gallery_images',
        'gallery_image_urls',
        'tags',
        'highlights',
        'included',
        'is_active',
        'sort_order',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'gallery_image_urls' => 'array',
            'tags' => 'array',
            'highlights' => 'array',
            'included' => 'array',
            'is_active' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getCardImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setCardImagePathAttribute(mixed $value): void
    {
        $this->attributes['card_image_path'] = UploadPath::normalize($value);
    }

    public function getDetailImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setDetailImagePathAttribute(mixed $value): void
    {
        $this->attributes['detail_image_path'] = UploadPath::normalize($value);
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

    public function getCardImageResolvedUrlAttribute(): ?string
    {
        return $this->resolveMediaPath($this->card_image_path) ?: MediaUrl::normalize($this->card_image_url);
    }

    public function getDetailImageResolvedUrlAttribute(): ?string
    {
        return $this->resolveMediaPath($this->detail_image_path)
            ?: MediaUrl::normalize($this->detail_image_url)
            ?: $this->card_image_resolved_url;
    }

    public function getGalleryResolvedUrlsAttribute(): array
    {
        return collect($this->gallery_images ?? [])
            ->map(fn ($path) => $this->resolveMediaPath($path))
            ->merge(collect($this->gallery_image_urls ?? [])->map(fn ($url) => MediaUrl::normalize($url)))
            ->filter()
            ->unique()
            ->values()
            ->all();
    }

    public function navigationPayload(): array
    {
        return [
            'label' => $this->title,
            'href' => route('bus-tour.listings.show', $this->slug),
        ];
    }

    public function cardPayload(): array
    {
        return [
            'key' => $this->slug,
            'title' => $this->title,
            'slug' => $this->slug,
            'href' => route('bus-tour.listings.show', $this->slug),
            'day' => $this->route_label,
            'price' => $this->price,
            'panelPrice' => $this->panel_price ?: $this->price,
            'label' => $this->category_label,
            'copy' => $this->short_description,
            'bestFor' => $this->best_for,
            'tags' => $this->tags ?? [],
            'highlights' => $this->highlights ?? [],
            'included' => $this->included ?? [],
            'cardImageUrl' => $this->card_image_resolved_url,
            'detailImageUrl' => $this->detail_image_resolved_url,
        ];
    }

    protected function resolveMediaPath(?string $path): ?string
    {
        if (! $path) {
            return null;
        }

        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return MediaUrl::normalize($path);
        }

        return MediaUrl::upload($path);
    }
}
