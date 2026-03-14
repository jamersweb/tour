<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Storage;

class Collection extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'summary',
        'description',
        'hero_image_path',
        'seo_title',
        'seo_description',
        'sort_order',
        'is_featured',
    ];

    protected function casts(): array
    {
        return [
            'is_featured' => 'boolean',
        ];
    }

    public function getHeroImageUrlAttribute(): ?string
    {
        if (! $this->hero_image_path) {
            return null;
        }

        if (str_starts_with($this->hero_image_path, 'http://') || str_starts_with($this->hero_image_path, 'https://')) {
            return $this->hero_image_path;
        }

        return Storage::disk('public')->url($this->hero_image_path);
    }

    public function experiences(): BelongsToMany
    {
        return $this->belongsToMany(Experience::class)
            ->withTimestamps()
            ->orderByPivot('sort_order');
    }
}
