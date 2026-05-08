<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Review extends Model
{
    protected $fillable = [
        'reviewable_type',
        'reviewable_id',
        'author_name',
        'rating',
        'title',
        'quote',
        'tag',
        'source',
        'sort_order',
        'is_featured',
        'is_published',
        'reviewed_at',
    ];

    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'sort_order' => 'integer',
            'is_featured' => 'boolean',
            'is_published' => 'boolean',
            'reviewed_at' => 'datetime',
        ];
    }

    public function reviewable(): MorphTo
    {
        return $this->morphTo();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }
}
