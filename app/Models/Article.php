<?php

namespace App\Models;

use App\Support\MediaUrl;
use App\Support\UploadPath;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Article extends Model
{
    protected $fillable = [
        'blog_category_id',
        'title',
        'slug',
        'category',
        'excerpt',
        'content',
        'hero_image_path',
        'read_time',
        'sort_order',
        'is_featured',
        'is_published',
        'published_at',
        'seo_title',
        'seo_description',
    ];

    protected function casts(): array
    {
        return [
            'read_time' => 'integer',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Article $article): void {
            if ($article->blog_category_id && (! $article->category || $article->isDirty('blog_category_id'))) {
                $article->category = BlogCategory::query()
                    ->whereKey($article->blog_category_id)
                    ->value('name') ?: $article->category;
            }
        });
    }

    public function blogCategory(): BelongsTo
    {
        return $this->belongsTo(BlogCategory::class);
    }

    public function getCategoryNameAttribute(): string
    {
        return $this->blogCategory?->name ?: $this->category;
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query
            ->where('is_published', true)
            ->where(function (Builder $query) {
                $query->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        if (! $this->hero_image_path) {
            return null;
        }

        if (str_starts_with($this->hero_image_path, 'http://') || str_starts_with($this->hero_image_path, 'https://')) {
            return MediaUrl::normalize($this->hero_image_path);
        }

        return MediaUrl::upload($this->hero_image_path);
    }

    public function getHeroImagePathAttribute(?string $value): ?string
    {
        return UploadPath::normalize($value);
    }

    public function setHeroImagePathAttribute(mixed $value): void
    {
        $this->attributes['hero_image_path'] = UploadPath::normalize($value);
    }
}
