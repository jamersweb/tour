<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceInquiry extends Model
{
    protected $fillable = [
        'experience_id',
        'user_id',
        'name',
        'email',
        'phone',
        'travel_date',
        'guest_count',
        'interest',
        'experience_title',
        'message',
        'source',
        'source_url',
        'status',
        'contacted_at',
        'follow_up_at',
        'admin_notes',
    ];

    protected function casts(): array
    {
        return [
            'travel_date' => 'date',
            'guest_count' => 'integer',
            'contacted_at' => 'datetime',
            'follow_up_at' => 'datetime',
        ];
    }

    public function experience(): BelongsTo
    {
        return $this->belongsTo(Experience::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeOpenPipeline(Builder $query): Builder
    {
        return $query->whereNotIn('status', ['won', 'lost']);
    }

    public function scopeOverdueFollowUp(Builder $query): Builder
    {
        return $query
            ->openPipeline()
            ->whereNotNull('follow_up_at')
            ->where('follow_up_at', '<', now());
    }

    public function scopeDueToday(Builder $query): Builder
    {
        return $query
            ->openPipeline()
            ->whereNotNull('follow_up_at')
            ->whereDate('follow_up_at', today());
    }
}
