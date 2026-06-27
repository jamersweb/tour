<?php

namespace App\Support;

use App\Models\Collection as TourCollection;

class ExperienceCollectionFilters
{
    /**
     * @return array<string, string>
     */
    public static function locations(): array
    {
        return self::forGroup('location');
    }

    /**
     * @return array<string, string>
     */
    public static function activities(): array
    {
        return self::forGroup('activity');
    }

    /**
     * @return array<string, array<string, string>>
     */
    public static function grouped(): array
    {
        return [
            'location' => self::locations(),
            'activity' => self::activities(),
        ];
    }

    /**
     * @return array<string, string>
     */
    private static function forGroup(string $group): array
    {
        return TourCollection::query()
            ->where('collection_group', $group)
            ->where('is_featured', true)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->pluck('name', 'slug')
            ->all();
    }
}
