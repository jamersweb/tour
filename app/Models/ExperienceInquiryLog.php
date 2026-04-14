<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ExperienceInquiryLog extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'experience_inquiry_id',
        'user_id',
        'action',
        'description',
        'properties',
    ];

    protected function casts(): array
    {
        return [
            'properties' => 'array',
            'created_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ExperienceInquiryLog $log): void {
            if ($log->created_at === null) {
                $log->created_at = now();
            }
        });
    }

    public function experienceInquiry(): BelongsTo
    {
        return $this->belongsTo(ExperienceInquiry::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
