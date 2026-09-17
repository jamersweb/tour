<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\UploadPath;
use Illuminate\Database\Eloquent\Model;

class StaticPage extends Model
{
    protected $fillable = [
        'page_key',
        'admin_title',
        'route_uri',
        'is_active',
        'seo_title',
        'seo_description',
        'hero_eyebrow',
        'hero_title',
        'hero_description',
        'hero_image_path',
        'hero_image_url',
        'primary_cta_label',
        'primary_cta_url',
        'secondary_cta_label',
        'secondary_cta_url',
        'sidebar_label',
        'sidebar_title',
        'sidebar_body',
        'sidebar_items',
        'metrics',
        'content_sections',
        'faqs',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'sidebar_items' => 'array',
            'metrics' => 'array',
            'content_sections' => 'array',
            'faqs' => 'array',
        ];
    }

    public static function forKey(string $key, array $defaults = []): self
    {
        return static::query()->firstOrCreate(
            ['page_key' => $key],
            static::recordDefaults($key, $defaults),
        );
    }

    public function getHeroImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setHeroImagePathAttribute(mixed $value): void
    {
        $this->attributes['hero_image_path'] = UploadPath::normalize($value);
    }

    public function getHeroImageResolvedUrlAttribute(): ?string
    {
        if ($this->hero_image_path) {
            return MediaUrl::upload($this->hero_image_path);
        }

        return MediaUrl::normalize($this->hero_image_url);
    }

    public function publicPayload(): array
    {
        return [
            'key' => $this->page_key,
            'title' => $this->admin_title,
            'routeUri' => $this->route_uri,
            'isActive' => $this->is_active,
            'seo' => [
                'title' => $this->seo_title,
                'description' => $this->seo_description,
            ],
            'hero' => [
                'eyebrow' => $this->hero_eyebrow,
                'title' => $this->hero_title,
                'description' => $this->hero_description,
                'imageUrl' => $this->hero_image_resolved_url,
            ],
            'primaryCta' => [
                'label' => $this->primary_cta_label,
                'url' => $this->primary_cta_url,
            ],
            'secondaryCta' => [
                'label' => $this->secondary_cta_label,
                'url' => $this->secondary_cta_url,
            ],
            'sidebar' => [
                'label' => $this->sidebar_label,
                'title' => $this->sidebar_title,
                'body' => $this->sidebar_body,
                'items' => $this->sidebar_items ?? [],
            ],
            'metrics' => $this->metrics ?? [],
            'sections' => $this->content_sections ?? [],
            'faqs' => $this->faqs ?? [],
        ];
    }

    public static function recordDefaults(string $key, array $defaults = []): array
    {
        return [
            'admin_title' => $defaults['admin_title'] ?? str($key)->replace('-', ' ')->title()->toString(),
            'route_uri' => $defaults['route_uri'] ?? null,
            'is_active' => true,
            'seo_title' => $defaults['seo']['title'] ?? null,
            'seo_description' => $defaults['seo']['description'] ?? null,
            'hero_eyebrow' => $defaults['hero']['eyebrow'] ?? null,
            'hero_title' => $defaults['hero']['title'] ?? null,
            'hero_description' => $defaults['hero']['description'] ?? null,
            'hero_image_url' => $defaults['hero']['imageUrl'] ?? null,
            'primary_cta_label' => $defaults['primaryCta']['label'] ?? null,
            'primary_cta_url' => $defaults['primaryCta']['url'] ?? null,
            'secondary_cta_label' => $defaults['secondaryCta']['label'] ?? null,
            'secondary_cta_url' => $defaults['secondaryCta']['url'] ?? null,
            'sidebar_label' => $defaults['sidebar']['label'] ?? null,
            'sidebar_title' => $defaults['sidebar']['title'] ?? null,
            'sidebar_body' => $defaults['sidebar']['body'] ?? null,
            'sidebar_items' => $defaults['sidebar']['items'] ?? null,
            'metrics' => $defaults['metrics'] ?? null,
            'content_sections' => $defaults['sections'] ?? null,
            'faqs' => $defaults['faqs'] ?? null,
        ];
    }
}
